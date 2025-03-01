<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueryMessageException extends Exception
{
    protected $queryException;

    /**
     * Constructor to accept QueryException
     */
    public function __construct($queryException)
    {
        parent::__construct($queryException->getMessage(), $queryException->getCode());
        $this->queryException = $queryException;
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request) :JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $this->getDatabaseErrorMessage(),
            'code'    => $this->queryException->getCode(),
        ], 500);
    }

    /**
     * Extract only the SQL error message and format column names.
     */
    private function getDatabaseErrorMessage(): string
    {
        $errorMessage = $this->queryException->errorInfo[2] ?? 'A database error occurred';

        // Extract the key name from the error message
        if (preg_match("/key '([^']+)'/", $errorMessage, $matches)) {
            $dbColumn = $matches[1]; // Extracted column name

            // Convert column name: Remove underscores & Capitalize words
            $formattedColumn = ucwords(str_replace('_', ' ', $dbColumn));

            // Replace in the error message
            $errorMessage = str_replace($dbColumn, $formattedColumn, $errorMessage);
        }

        return $errorMessage;
    }
}
