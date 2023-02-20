<?php

namespace GenerCode\Commands;

class InstallCommand extends GenericCommand
{
    protected $description = 'Installs the latest version of the files';
    protected $signature = "gc:install";



    public function handle()
    {
        try {
             //if hosting, run before cleaning up
            $this->call("gc:download");
            $this->call("migrate", ["--path"=>$this->download_dir . "/migrations"]);
            $this->call("gc:cdn");
            $this->info("Install Completed");
            //unlink($zip_name);
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
