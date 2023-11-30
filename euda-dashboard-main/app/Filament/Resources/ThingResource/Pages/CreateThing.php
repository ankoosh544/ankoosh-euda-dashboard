<?php

namespace App\Filament\Resources\ThingResource\Pages;

use App\Filament\Resources\ThingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Aws\Iot\IotClient;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log; 
use ZipArchive;

class CreateThing extends CreateRecord
{
    protected static string $resource = ThingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Create the IoT Thing and retrieve the ZIP file information
        $response = $this->createThing($data);

        //dd($response);die();
    
        // If there was an issue creating the Thing, handle the error
        if (isset($response['error'])) {
            // Log or handle the error as needed
            return response()->json(['error' => $response['error']], 500);
        }
    
        // Store the zip file content in the database
        $data['file_name'] = $response['file_name'];
        // Store the zip file path in the database
        $data['file_path'] = $response['file_path'];
    
        // Return the updated data
        return $data;
    }
    
    private function createThing($data)
    {
        $thingName = $data['thing_name'];
        $thingType = $data['thing_type'];
        $plantId = $data['plantId'];
    
        $attributes = ['plantId' => $plantId];
    
        $iotClient = new IotClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    
        try {
            $result = $iotClient->createThing([
                'thingName' => $thingName,
                'thingTypeName' => $thingType,
                'attributePayload' => ['attributes' => $attributes],
            ]);
    
            $resultCertificate = $iotClient->createKeysAndCertificate([
                'setAsActive' => true,
            ]);
    
            $iotClient->attachThingPrincipal([
                'thingName' => $thingName,
                'principal' => $resultCertificate['certificateArn'],
            ]);
    
            $policies = $iotClient->listPolicies();
            $existingPolicy = collect($policies['policies'])->firstWhere('policyName', 'all-permissions-policy');
    
            if ($existingPolicy) {
                $iotClient->attachPolicy([
                    'policyName' => $existingPolicy['policyName'],
                    'target' => $resultCertificate['certificateArn'],
                ]);
            } else {
                throw new Exception('Policy not found');
            }
    
            $zipFileName = "{$thingName}-certificates.zip";
            $zipPath = storage_path("app/public/{$zipFileName}");
    
            $zip = new ZipArchive;
    
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFromString('certificate.pem', $resultCertificate['certificatePem']);
                $zip->addFromString('privatekey.pem', $resultCertificate['keyPair']['PrivateKey']);
                $zip->addFromString('CA_certificate.pem', file_get_contents('https://www.amazontrust.com/repository/AmazonRootCA1.pem'));
                $zip->close();
    
                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);
    
                $datetime = now()->format('Y-m-d');
                $datetimeForS3Key = str_replace([' ', ':'], ['_', '-'], $datetime);
                $s3Key = "certificates/{$datetimeForS3Key}/{$zipFileName}";
    
                $s3->putObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key' => $s3Key,
                    'Body' => file_get_contents($zipPath),
                ]);
                
                return ['file_path' => $s3Key, 'file_name' => $zipFileName];
            } else {
                throw new Exception('Failed to create ZIP archive: ' . $zip->getStatusString());
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
}
