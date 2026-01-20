<?php

namespace App\Livewire;

use LikeABas\FilamentChatgptAgent\ChatgptChat;
use LikeABas\FilamentChatgptAgent\Components\ChatgptAgent as BaseChatgptAgent;
use OpenAI\Exceptions\TransporterException;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;
use TypeError;
use Exception;

class CustomChatgptAgent extends BaseChatgptAgent
{
    protected function getSessionKey(): string
    {
        // Generate the session key the same way the parent class does
        return auth()->id() . '-chatgpt-agent-messages';
    }
    protected function chat(): void
    {
        try {
            $chat = new ChatgptChat();
            $chat->loadMessages($this->messages);
            if ($this->pageWatcherEnabled) {
                $chat->addMessage(filament('chatgpt-agent')->getPageWatcherMessage() . $this->questionContext);
                \Log::info($this->questionContext);
            }

            $chat->send();

            $this->messages[] = ['role' => 'assistant', 'content' => $chat->latestMessage()->content];

            $sessionKey = $this->getSessionKey();
            request()->session()->put($sessionKey, $this->messages);
        } catch (TransporterException $e) {
            \Log::error('OpenAI API Transport Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            
            $errorMessage = 'Sorry, there was an error connecting to the AI service. Please check your API configuration or try again later.';
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMessage];
            $sessionKey = $this->getSessionKey();
            request()->session()->put($sessionKey, $this->messages);
        } catch (OpenAIErrorException $e) {
            \Log::error('OpenAI API Error: ' . $e->getMessage(), [
                'exception' => $e,
                'response' => $e->response ?? null,
            ]);
            
            $errorMessage = 'Sorry, there was an error processing your request. ' . ($e->getMessage() ?: 'Please try again later.');
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMessage];
            $sessionKey = $this->getSessionKey();
            request()->session()->put($sessionKey, $this->messages);
        } catch (TypeError $e) {
            \Log::error('OpenAI Response Type Error: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = 'Sorry, there was an error processing the AI response. This may be due to an API configuration issue. Please check your OpenAI API key and settings.';
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMessage];
            $sessionKey = $this->getSessionKey();
            request()->session()->put($sessionKey, $this->messages);
        } catch (Exception $e) {
            \Log::error('ChatGPT Agent Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = 'Sorry, an unexpected error occurred. Please try again later.';
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMessage];
            $sessionKey = $this->getSessionKey();
            request()->session()->put($sessionKey, $this->messages);
        }
    }
}

