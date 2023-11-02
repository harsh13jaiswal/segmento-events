<?php

namespace App\Libs;

use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BigqueryLib{
    protected $table;
    protected $dataset;
    protected $client;

    public function __construct() {
        $credentials=Storage::get('key/auth.json');
        $credentials=[
            "type"=> "service_account",
            "project_id"=> "via-socket-prod",
            "private_key_id"=> "46b0e0fc558e35db7c069acee6b05b0d224e1fd9",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEuwIBADANBgkqhkiG9w0BAQEFAASCBKUwggShAgEAAoIBAQC3y+CA24gQ8KnO\nHSxVb5OOUJ60Dq/XmQddAcJeJQVXMzkODZydOpl1SRFruII4InX9x7JEuXZD82NU\nK3t36ZWULyzcqTXAzASQOdzoJDmJH/8qSY2bI6iYvRhwnXxrKzjVQOmcUA+wh2Qc\n1nAtOJi8wKXcRiLrFhXrLE06jwopB0WXEP4YX6Ry6hZ6iAo+MyLmCFJB6ZlOwNTL\nul4B8kNZ1AhIIcCD4EJYQZbw985oMigEdI33Se6lOfpVl0oKCKKKUtJak/aT3B2a\nEy9cuwEO6uDrti7kpid/IDvDhnBoNDQHgsOQjaU1ZEaQUijw1x/UqKSpkb31N5u/\nMVY4ll7lAgMBAAECgf9cyCSGl5SC4ilgrieJWiwuJe08+BATVxzhRb51Gjf7yeZS\nWg8mUZOtessJsvxDkOfyjUo7sUcUMdmYg7NfwE3L1crJZk5W65HsojqoMTCGkvkB\nJFPaNy4W7lmOufgOi+0C8qzxyxGzDTbOuix3n4Zi+vcwVSxQPoI3h/IsFK2NKZs4\nsepRvCTcuRzz/mrEq4xwHnu8Zsn6gRDD7Cli8T07lRXDTrAIz+03pLPhhIZzfy6F\ndTwXN1V8TaG2FCm6Su8kEGVa6cRpA2NgED4vSp7lcwpvQulZFTDD0oKD8sE8Nps2\n0NmAhjGYVD+tc8KKfRQBznCXzMWsqr/7cTdB41ECgYEA/IajT3Cocp40jCgTHN2O\nEBQnlt2fX4no4Ui/cZAo0B5Ef25fvP9zWcxmNy0XW725nyu0rNSagSIH4BZn1xJT\njobkFvUbdrNLKL8fUVTR71V/WzxithJVSeerMyEk1DJ+lhA0WARDv7M9lb3DfHdS\nvWWA1NQN/LiJCXpkpuFtK0MCgYEAulMu1OMp0X0tKs7LPhHronDPiHL1I8+8vdd8\nZryehiv6xaAFC7LD/yM1gj8BSB5969tellsaETlpbF7OS+6Oly27snSX/UF34/B4\nlebx/ST5QHIBEfO3YUg1FmRzk2/RPYY0lEfvZtEstMvMlhAMY9HLgVNx82PkMyTy\nxv8VprcCgYBXNoCsbPIgM7deOHDxZSstLmjF1+C09EIznBZSOEGALxPlFs+FzIug\nFdGveKk6i/nRmRybHAoIUyJ5KAPQ6YlmDfw0WY6UnjN07Rz5z9t5VwPXFLHaw9Yk\n4hfkXqwDhTTmys3pH//t8w9v6cvb7rHqq2WlG1+BSpI5bcXZRL2ZVQKBgQCbuxrR\nZGx3Y6B0vxKwdln0E0XiTfMGU4L1ST34wH3etrOKqgyNkoSuosb+bZqspI+qqleM\nY+iNrOaoZTUX0fPr95WBumGukyGZqkufPr/TTSvm6WJrlsAW1ztH0/2lpTfFrH4V\n0WoPDZXIJu6AHjm81IS7Ovtq6nq5JJCmMl3uUQKBgChjAzRzxACjDwLbFxIwtfJ+\nh8TWoyLMvWIEbRdhsf+4UsBWtcund6GcSNxGBXu05kk43wxH/MkVB0s9/2lbTNhg\npWSdr8V1LlTxWMAivJmMn+Bjp0y2lFr6ptDEiqEAF/lsf7lICc4hZjqy4WB9TWQv\nqB+JdNIgV+mnnZoyeO8C\n-----END PRIVATE KEY-----\n",
            "client_email"=> "big-query@via-socket-prod.iam.gserviceaccount.com",
            "client_id"=> "105985983879219923000",
            "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/big-query%40via-socket-prod.iam.gserviceaccount.com",
            "universe_domain"=> "googleapis.com"
        ];
        $this->client = new BigQueryClient(["keyFile" => $credentials, "projectId" => "via-socket-prod"]);
    }

    public function runQuery($query){
        $queryJobConfig = $this->client->query($query);
        $queryResults = $this->client->runQuery($queryJobConfig);

        $results = []; // Initialize an empty array for storing results

        if ($queryResults->isComplete()) {
            // Get the rows from the result
            $rows = $queryResults->rows();
            dd($rows);
            // Process each row
            foreach ($rows as $row) {
                $resultRow = [];
                // Access each field in the row
                foreach ($row as $field => $value) {
                    // Store the field and value in the current row
                    $resultRow[$field] = $value;
                }
                // Store the current row in the results array
                $results[] = $resultRow;
            }
        } else {
           return null;
        }
        return $results;

    }




}
