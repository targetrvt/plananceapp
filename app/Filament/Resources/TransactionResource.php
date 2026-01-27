<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;
    
    public static function getNavigationLabel(): string
    {
        return __('transaction.navigation.label');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('transaction.navigation.label');
    }
    
    public static function getModelLabel(): string
    {
        return __('transaction.navigation.label');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Management'; // Must match the group name registered in AppPanelProvider
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('transaction.form.receipt_upload.section'))
                    ->schema([
                        Forms\Components\FileUpload::make('receipt_image')
                            ->label(__('transaction.form.receipt_upload.upload_receipt.label'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('receipts')
                            ->visibility('public')
                            ->helperText(__('transaction.form.receipt_upload.upload_receipt.helper'))
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) return;
                                
                                $path = $state instanceof TemporaryUploadedFile 
                                    ? $state->getRealPath()
                                    : public_path('storage/' . $state);
                                    
                                if (!file_exists($path)) return;
                                
                                try {
                                    // Process the image with AI
                                    $extractedData = self::processReceiptWithAI($path);
                                    
                                    // Update form fields with extracted data
                                    if (!empty($extractedData)) {
                                        if (isset($extractedData['type'])) {
                                            $set('type', strtolower($extractedData['type']));
                                        } else {
                                            // Default to expense for receipts
                                            $set('type', 'expense');
                                        }
                                        
                                        if (isset($extractedData['amount'])) {
                                            $set('amount', $extractedData['amount']);
                                        }
                                        
                                        if (isset($extractedData['date'])) {
                                            $set('date', $extractedData['date']);
                                        }
                                        
                                        if (isset($extractedData['description'])) {
                                            $set('description', $extractedData['description']);
                                        }
                                        
                                        if (isset($extractedData['category'])) {
                                            $set('category', $extractedData['category']);
                                        } else {
                                            // Set default category based on type
                                            $set('category', $extractedData['type'] === 'income' ? 'other_income' : 'other_expense');
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Log the error
                                    \Log::error('Receipt processing error: ' . $e->getMessage());
                                }
                            })
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('transaction.form.transaction_details.section'))
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('transaction.form.transaction_details.type.label'))
                            ->options([
                                'income' => __('transaction.form.transaction_details.type.options.income'),
                                'expense' => __('transaction.form.transaction_details.type.options.expense'),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('transaction.form.transaction_details.amount.label'))
                            ->required()
                            ->numeric()
                            ->prefix('EUR'),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('transaction.form.transaction_details.date.label'))
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('category')
                            ->label(__('transaction.form.transaction_details.category.label'))
                            ->options([
                                // Income categories
                                'salary' => __('messages.categories.income.salary'),
                                'investment' => __('messages.categories.income.investment'),
                                'gift' => __('messages.categories.income.gift'),
                                'refund' => __('messages.categories.income.refund'),
                                'other_income' => __('messages.categories.income.other_income'),
                                
                                // Expense categories
                                'food' => __('messages.categories.expense.food'),
                                'shopping' => __('messages.categories.expense.shopping'),
                                'entertainment' => __('messages.categories.expense.entertainment'),
                                'transportation' => __('messages.categories.expense.transportation'),
                                'housing' => __('messages.categories.expense.housing'),
                                'utilities' => __('messages.categories.expense.utilities'),
                                'health' => __('messages.categories.expense.health'),
                                'education' => __('messages.categories.expense.education'),
                                'travel' => __('messages.categories.expense.travel'),
                                'unhealthy_habits' => __('messages.categories.expense.unhealthy_habits'),
                                'other_expense' => __('messages.categories.expense.other_expense'),
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('transaction.form.transaction_details.description.label'))
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
                Forms\Components\Hidden::make('receipt_path')
                    ->dehydrateStateUsing(fn ($state, $record) => 
                        $record && $record->receipt_image ? $record->receipt_image : null
                    ),
            ]);
    }

    protected static function processReceiptWithAI($imagePath)
{
    // Read image file and convert to base64
    $imageData = base64_encode(file_get_contents($imagePath));
    
    // Use a more specific prompt with expected format
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'You are a receipt data extraction expert. Extract exactly these fields from this receipt image:
                            1. transaction_type: (must be either "income" or "expense" only)
                            2. amount: (numeric value only, no currency symbols)
                            3. date: (in YYYY-MM-DD format)
                            4. description: (merchant name or brief transaction description)
                            5. category: (categorize this transaction as one of: salary, investment, gift, refund, other_income, food, shopping, entertainment, transportation, housing, utilities, health, education, travel, unhealthy_habits, other_expense)
                            
                            IMPORTANT: If the receipt is for cigarettes, alcohol, tobacco, vaping products, or similar unhealthy items, categorize it as "unhealthy_habits".
                            
                            Return ONLY a valid JSON object with these exact keys: {"type": "", "amount": "", "date": "", "description": "", "category": ""}'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$imageData}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 300
        ]);
    
        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            
            // Better JSON extraction and validation
            try {
                // First attempt: direct JSON parsing
                $data = json_decode($content, true);
                
                // If failed, try to extract JSON from text
                if (json_last_error() !== JSON_ERROR_NONE) {
                    preg_match('/\{.*\}/s', $content, $matches);
                    if (!empty($matches)) {
                        $data = json_decode($matches[0], true);
                    }
                }
                
                // Validate required fields
                if (json_last_error() === JSON_ERROR_NONE && 
                    isset($data['type']) && isset($data['amount'])) {
                    
                    // Normalize the data
                    $data['type'] = strtolower($data['type']);
                    if (!in_array($data['type'], ['income', 'expense'])) {
                        $data['type'] = 'expense'; // Default to expense for receipts
                    }
                    
                    // Convert amount to numeric value
                    $data['amount'] = (float) preg_replace('/[^0-9.]/', '', $data['amount']);
                    
                    return $data;
                }
            } catch (\Exception $e) {
                \Log::error('JSON parsing error: ' . $e->getMessage());
            }
        }
        
            \Log::error('AI processing failed or returned invalid data', [
                'response' => $response->json() ?? 'No JSON response',
                'status' => $response->status()
        ]);
        
        return [];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->where('user_id', auth()->id())) // Filter by user_id
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('transaction.table.date.label'))
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('transaction.table.type.label'))
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('transaction.table.amount.label'))
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('transaction.table.category.label'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'salary' => __('messages.categories.income.salary'),
                        'investment' => __('messages.categories.income.investment'),
                        'gift' => __('messages.categories.income.gift'),
                        'refund' => __('messages.categories.income.refund'),
                        'other_income' => __('messages.categories.income.other_income'),
                        'food' => __('messages.categories.expense.food'),
                        'shopping' => __('messages.categories.expense.shopping'),
                        'entertainment' => __('messages.categories.expense.entertainment'),
                        'transportation' => __('messages.categories.expense.transportation'),
                        'housing' => __('messages.categories.expense.housing'),
                        'utilities' => __('messages.categories.expense.utilities'),
                        'health' => __('messages.categories.expense.health'),
                        'education' => __('messages.categories.expense.education'),
                        'travel' => __('messages.categories.expense.travel'),
                        'unhealthy_habits' => __('messages.categories.expense.unhealthy_habits'),
                        'other_expense' => __('messages.categories.expense.other_expense'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'salary', 'investment', 'gift', 'refund', 'other_income', 
                        'food', 'transportation', 'housing', 'utilities', 
                        'health', 'education', 'travel' => 'success',
                        'unhealthy_habits' => 'danger',
                        'shopping', 'entertainment', 'other_expense' => 'gray',
                        default => 'gray',})
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('transaction.table.description.label'))
                    ->limit(50)
                    ->searchable()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('transaction.filter.type.label'))
                    ->options([
                        'income' => __('transaction.filter.type.options.income'),
                        'expense' => __('transaction.filter.type.options.expense'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_receipt')
                    ->label(__('transaction.actions.view_receipt.label'))
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->url(fn (Transaction $record) => $record->receipt_image 
                        ? asset('storage/' . $record->receipt_image) 
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Transaction $record) => $record->receipt_image !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}