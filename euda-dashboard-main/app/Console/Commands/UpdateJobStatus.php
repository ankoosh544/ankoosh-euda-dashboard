<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Cache;
use Aws\Iot\IotClient;
use Illuminate\Http\Request;
use PhpMqtt\Client\MQTTClient;
use Aws\IotJobsDataPlane\IotJobsDataPlaneClient;
use Illuminate\Support\Facades\Log;


class UpdateJobStatus extends Command
{
    protected $signature = 'app:update-job-status';
    protected $description = 'Update Job status';

    public function handle()
    {
       
        $client = new IotClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);


        try {
            $jobs = $client->listJobs([]);
            $jobStatuses = [];
            
            foreach ($jobs['jobs'] as $job) {
                $jobId = $job['jobId'];
            
                try {
                    $result = $client->describeJob([
                        'jobId' => $jobId,
                    ]);
                    $testResult = $client->getJobDocument(['jobId' => $jobId]);
            
                    $jobStatus = $result['job']['status'];
                    $thingType = null;
                    $version = null;
            
                    if (isset($testResult['document'])) {
                        $decodedData = json_decode($testResult['document'], true);
                        $thingType = $decodedData['EUDA_BOARD'];
                        $version = $decodedData['major_version'] . '.' . $decodedData['minor_version'] . '.' . $decodedData['patch_version'];
                    }
            
                } catch (\Exception $e) {
                    Log::error('Error while describing job or getting job document: ' . $e->getMessage());
                    $jobStatus = 'Error';
                }
            
                $jobStatuses[$jobId] = $jobStatus;
            
                $existingJob = \DB::table('jobs')
                    ->where('job_id', $jobId)
                    ->first();
            
                if ($existingJob) {
                    \DB::table('jobs')
                        ->where('job_id', $jobId)
                        ->update([
                            'status' => $jobStatus
                        ]);
                } 
            }
        } catch (\Exception $e) {
            Log::error('Error while listing jobs: ' . $e->getMessage());
            $this->error('Error while monitoring IoT jobs: ' . $e->getMessage());
        }
        

           
    }

}
