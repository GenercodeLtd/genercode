<?php

namespace GenerCode\Controllers;

use \GenerCode\Exceptions as Exceptions;
use \GenerCode\Models\Audit;



class AuditController
{
    //audit functions

    public function get(Request $request, $name, $id)
    {
        $audit = new Audit();
        $audit
            ->where("model_id", "=", $id)
            ->where("model", "=", $name)
            ->where("date_created", "<=", $date)
            ->order("date_created", "ASC");

        if ($request->has("--created")) {
            $audit->where("date_created", "<=", $request->get("--created"));
        }
        $rows = $audit->get()->toArray();

        if (count($rows) == 0) return null;

        $hist = [];
        foreach($rows as $row) {
            $log = json_decode($row->log, true);
            foreach($log as $key=>$val) {
                $hist[$key] = $val;
            }
        }
        return $hist;
    }


    public function getSince(Request $request, $name, $id) {
        $audit = new Audit();
        return $audit
            ->where("model_id", "=", $id)
            ->where("model", "=", $name)
            ->where("date_created", ">=", $request->get("--created"))
            ->order("date_created", "ASC")
            ->limit(1)
            ->exists();
    }


    public function getDeleted(Request $request, $name, $date) {
        $audit = new Audit();
        return $audit
            ->where("action", "=", "DELETE")
            ->where("model", "=", $name)
            ->where("date_created", ">=", $request->get("--created"))
            ->get()
            ->toArray();
    }

}
