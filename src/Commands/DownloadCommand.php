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

   
    function extractFiles($zip, array $excludes = []) {
        $files = [];
        for ( $i=0; $i < $zip->numFiles; ++$i ) {
            $entry = $zip->getNameIndex($i);

            if (!$this->isExcluded($entry, $excludes)) {
                $files[] = $zip->getNameIndex($i);
            }
        }
        $zip->extractTo($this->download_dir, $files);
    }


    public function handle()
    {
        try {
            $this->login();
            $blob = $this->http->get("/asset/projects/src/" . $this->project_id);

            file_put_contents($this->download_dir . "/src.zip", (string) $blob);

            $zip = new \ZipArchive();
            $zip->open($this->download_dir . "/src.zip");
            $this->extractFiles($zip, config("cmd.download_excludes", []));
            $zip->close();

            unlink($this->download_dir . "/src.zip");
            $this->info("Upload Completed");

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
