<?php

namespace App\Services\Ai;

use Illuminate\Support\Arr;
use MalteKuhr\LaravelGPT\Enums\ChatRole;
use MalteKuhr\LaravelGPT\Exceptions\GPTChat\ErrorPatternFoundException;
use MalteKuhr\LaravelGPT\Exceptions\GPTChat\NoFunctionCallException;
use MalteKuhr\LaravelGPT\Exceptions\GPTFunction\FunctionCallRequiresFunctionsException;
use MalteKuhr\LaravelGPT\Exceptions\GPTFunction\MissingFunctionException;
use MalteKuhr\LaravelGPT\Facades\OpenAI;
use MalteKuhr\LaravelGPT\Generators\ChatPayloadGenerator;
use MalteKuhr\LaravelGPT\GPTChat;
use MalteKuhr\LaravelGPT\Managers\ChatManager;
use MalteKuhr\LaravelGPT\Managers\FunctionManager;
use MalteKuhr\LaravelGPT\Models\ChatMessage;
use OpenAI\Exceptions\TransporterException;

/**
 * Extends Laravel-GPT ChatManager so each OpenAI completion records token usage once.
 *
 * Parent {@see ChatManager} uses `self::send()` inside {@see ChatManager::handleWrongFunctionCall}
 * and {@see ChatManager::handleFunctionCall}, which would bypass this class. Those methods are
 * overridden here to recurse with {@see send()}.
 */
class TrackedChatManager extends ChatManager
{
    protected function __construct(
        GPTChat $chat,
        private readonly AiUsageRecorder $recorder,
        private readonly ?int $userId,
        private readonly string $usageFeatureKey,
    ) {
        parent::__construct($chat);
    }

    public static function makeTracked(
        GPTChat $chat,
        ?int $userId,
        string $usageFeatureKey,
    ): static {
        return new self(
            $chat,
            app(AiUsageRecorder::class),
            $userId,
            $usageFeatureKey,
        );
    }

    /**
     * @throws FunctionCallRequiresFunctionsException
     * @throws MissingFunctionException
     * @throws TransporterException
     * @throws ErrorPatternFoundException
     */
    public function send(): GPTChat
    {
        if (! $this->chat->sending()) {
            return $this->chat;
        }

        $payload = ChatPayloadGenerator::make($this->chat)->generate();
        $createResponse = OpenAI::chat()->create($payload);

        $this->recorder->recordFromOpenAiCreateResponse(
            $this->userId,
            $this->usageFeatureKey,
            $createResponse,
        );

        $answer = $createResponse->choices[0]->message;

        $this->handleResponse($answer);

        if (! $this->chat->received()) {
            return $this->chat;
        }

        if (isset($payload['function_call']) && $answer->functionCall?->name !== $payload['function_call']['name']
            && ! in_array($payload['function_call']['name'], ['auto', 'none'], true)) {

            return $this->handleWrongFunctionCall($payload);
        }

        $latestMessage = $this->chat->latestMessage();
        if ($latestMessage->role == ChatRole::ASSISTANT && $latestMessage->functionCall != null) {
            return $this->handleFunctionCall($latestMessage);
        }

        return $this->chat;
    }

    /**
     * @see ChatManager::handleWrongFunctionCall
     *
     * @throws ErrorPatternFoundException
     * @throws FunctionCallRequiresFunctionsException
     * @throws MissingFunctionException
     * @throws TransporterException
     */
    protected function handleWrongFunctionCall(array $payload): GPTChat
    {
        if (Arr::first($this->chat->messages, fn (ChatMessage $message) => $message->role == ChatRole::FUNCTION && $message->content == NoFunctionCallException::modelMessage())) {
            throw NoFunctionCallException::create();
        }

        $this->chat->addMessage(
            ChatMessage::from(
                role: ChatRole::FUNCTION,
                content: NoFunctionCallException::modelMessage(),
                name: $payload['function_call']['name']
            )
        );

        return $this->send();
    }

    /**
     * Duplicate of parent's {@see ChatManager::handleFunctionCall} replacing `self::send()` with `$this->send()`.
     *
     * @throws ErrorPatternFoundException
     * @throws FunctionCallRequiresFunctionsException
     * @throws MissingFunctionException
     * @throws TransporterException
     */
    protected function handleFunctionCall(ChatMessage $answer): GPTChat
    {
        $function = Arr::first(
            array: $this->chat->functions(),
            callback: fn (\MalteKuhr\LaravelGPT\GPTFunction $function) => $function->name() == $answer->functionCall->name
        );

        if ($function == null) {
            $this->chat->addMessage(
                ChatMessage::from(
                    role: ChatRole::FUNCTION,
                    content: [
                        'errors' => 'Function not found.',
                    ],
                    name: $answer->functionCall->name
                )
            );

            return $this->chat;
        }

        $this->chat->addMessage(
            FunctionManager::make($function)->call($answer->functionCall->arguments)
        );

        $this->noErrorPatternExits();

        $isForced = get_class($function) == $this->chat->functionCall();
        $hasResponse = $this->chat->latestMessage()->content !== null;
        $hasErrors = isset($this->chat->latestMessage()->content['errors']);

        if ((! $isForced && $hasResponse) || $hasErrors) {
            return $this->send();
        }

        return $this->chat;
    }
}
