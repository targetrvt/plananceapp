<?php

return [
    'navigation' => [
        'label' => 'Transactions',
        'group' => 'Management',
    ],
    'form' => [
        'receipt_upload' => [
            'section' => 'Receipt Upload',
            'upload_receipt' => [
                'label' => 'Upload Receipt',
                'helper' => 'Upload a receipt image to automatically extract transaction details',
            ],
        ],
        'transaction_details' => [
            'section' => 'Transaction Details',
            'type' => [
                'label' => 'Type',
                'options' => [
                    'income' => 'Income',
                    'expense' => 'Expense',
                ],
            ],
            'amount' => [
                'label' => 'Amount',
            ],
            'date' => [
                'label' => 'Date',
            ],
            'category' => [
                'label' => 'Category',
            ],
            'description' => [
                'label' => 'Description',
            ],
            'financial_goal' => [
                'label' => 'Goal',
            ],
        ],
    ],
    'table' => [
        'date' => [
            'label' => 'Date',
        ],
        'type' => [
            'label' => 'Type',
        ],
        'amount' => [
            'label' => 'Amount',
        ],
        'category' => [
            'label' => 'Category',
        ],
        'description' => [
            'label' => 'Description',
        ],
    ],
    'filter' => [
        'type' => [
            'label' => 'Type',
            'options' => [
                'income' => 'Income',
                'expense' => 'Expense',
            ],
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'New Transaction',
        ],
        'view_receipt' => [
            'label' => 'View Receipt',
        ],
    ],
    'import' => [
        'action_label' => 'Import from file',
        'modal_heading' => 'Import transactions',
        'modal_description' => 'Upload a CSV, Excel spreadsheet, or bank statement PDF. CSV/Excel rows should include at least Date and Amount columns (Debit/Credit or Type improves accuracy). PDFs use AI text extraction combined with structured parsing.',
        'file_label' => 'File',
        'helper' => 'CSV or Excel (.xlsx, .xls) columns we recognize include: Date, Amount, Type, Description, Debit, Credit, Payee/Merchant, Category (optional). Savings goal transfers are skipped for safety (cannot link a goal from a file).',
        'messages' => [
            'success_title' => 'Import finished',
            'failed_title' => 'Import failed',
            'summary' => 'Imported :imported row(s); skipped :skipped.',
            'unreadable' => 'Uploaded file could not be read.',
            'openai_missing' => 'OpenAI API key missing—PDF imports need it configured.',
            'pdf_empty_text' => 'No readable text extracted from PDF (try OCR export from your bank as CSV/PDF copy).',
            'ai_failed' => 'Could not analyse the PDF with AI right now.',
            'ai_invalid_json' => 'AI returned invalid JSON; try splitting the statement or exporting CSV from your bank.',
            'row_invalid_numbered' => 'Row #:n skipped (missing date/type/amount).',
            'row_save_failed' => 'Row #:n could not be saved.',
        ],
    ],
    'export' => [
        'bulk_csv_label' => 'Export CSV',
    ],
];
