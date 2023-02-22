<?php

namespace GenerCode\Commands;

class DownloadCommand extends GenericCommand
{
    protected $description = 'Downloads copy of API without repblushing';
    protected $signature = "gc:download";

    function isExcluded($file, $excludes) {
        foreach($excludes as $exclude) {
            if (strpos($file, $exclude) !== false) return true;
        }
        return false;
    }


    function copyDirectory($local_dir, $transfer_dir) {
        $dir = new \DirectoryIterator(
            base_path() . $this->download_dir . "/temp/" . $local_dir,
        );

        if (!file_exists($transfer_dir)) {
            mkdir($transfer_dir);
        }

        foreach($dir as $file) {
            if ($file->isDot()) continue;

            if (is_dir($file->getRealPath())) {
                if (!file_exists($file->getRealPath())) mkdir($file->getRealPath());
            } else {
                file_put_contents(
                    $transfer_dir . $file->getFilename(),
                    file_get_contents($file->getRealPath())
                );
            }
        }
    }
   
    function copyFiles() {
        $this->copyDirectory("api/Model",   app_path() . "/Models/");
        $this->copyDirectory("api/Profile",   app_path() . "/Profile/");
        $this->copyDirectory("api/Entity",   app_path() . "/Entity/");
        $this->copyDirectory("api/Dictionary",   app_path() . "/Dictionary/");
        $this->copyDirectory("api/Repository",   app_path() . "/Repository/");
        $this->copyDirectory("api/Resource",   app_path() . "/Resource/");
        $this->copyDirectory("api/Validation",   app_path() . "/Validation/");
        $this->copyDirectory("api/Controller",   app_path() . "/Http/Controllers/");
        $this->copyDirectory("migrations",   base_path() . "/database/migrations/");
        file_put_contents(
            base_path() . "/genercode.json", 
            file_get_contents(base_path() . $this->download_dir . "/temp/genercode.json")
        );
    }   


    public function handle()
    {
        try {
            $this->login();
            $api = new \GenerCodeClient\API($this->http, $this->api_type);
            $blob = $api->getAsset("projects", "src", $this->project_id);

            $zip_src = base_path() . $this->download_dir . "/src.zip";
            file_put_contents($zip_src, (string) $blob);
   
            $zip = new \ZipArchive();
            $zip->open($zip_src);
            $zip->extractTo(base_path() . $this->download_dir . "/temp");
            $zip->close();
            $this->copyFiles();
            unlink($zip_src);
            $this->info("Download Completed");

            //then we need to unzip the file
        } catch(\GenerCodeClient\ApiErrorException $e) {
            $this->error($e->getMessage());
            return 1;
          //  $output->writeln($e->getDetails());
        } catch (\Exception $e) {
            $this->error($e->getFile() . ": " . $e->getLine() . "\n" . $e->getMessage());
            return 2;
        }
    }
}
