<?php

namespace d3yii2\d3audittrail\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3audittrail\models\TblAuditTrail;
use d3yii2\d3audittrail\Module;
use yii\console\ExitCode;

/**
* Class MantainController* @property Module $module
*/
class MantainController extends D3CommandController
{

    /**
     * clear records who oldes given months
     * @return int
     */
    public function actionClearOld(int $months): int
    {

            $count = TblAuditTrail::deleteAll(
        '`stamp` < ADDDATE(NOW(),  INTERVAL -:months MONTH)',
                [
                    ':months' => $months,
                ]
            );

            $this->out('Deleted ' . $count . ' recods');
            return ExitCode::OK;
    }

}

