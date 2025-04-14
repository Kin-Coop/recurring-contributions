<?php

namespace Civi\Api4\Action\Kinrc;

use Civi\Api4\Generic\Result;

/**
 * Random example - demonstrates creating an action by extending `AbstractAction` directly.
 *
 * When building an action from scratch, one must define, at minimum, a `_run()` function, and optionally declare some parameters.
 *
 * **Tip:** Before you build a completely custom action like this, consider if you'd get more benefit from extending one of the Basic actions.
 *
 * Since this action doesn't really do anything, we don't need fancy query params like `select`, `where`, `orderBy` and `limit`,
 * but with a one-line change we could make this class extend `BasicGetAction` and we'd get all those features.
 * In that case we'd want to change from declaring a `_run()` function to extending our new parent's `getRecords` method,
 * but the rest could stay the same.
 *
 * **Note:** This action is _not_ declared in the `Example` entity, yet still appears in the Explorer, and can even be called like
 * `Civi\Api4\Example::random()->execute;` This works by magic, don't think too hard about it. All you need to know is that
 * one can add an action to any entity simply by placing a class like this in the `\Civi\Api4\Action\NameOfEntity` namespace.
 *
 * The reason this magic exists is so that one extension can mix actions into existing API entities (either in core or in other extensions).
 * Since we're doing this all within the same extension, there's no practical reason for relying on magic, and best practice
 * would be to define `static function random()` in our `Example` entity so it's easier for IDEs to discover.
 * But this is a neat trick when adding an action from one extension to another's entity.
 *
 * @see \Civi\Api4\Generic\AbstractAction
 *
 * @package Civi\Api4\Action\Example
 */
class Copycontribution extends \Civi\Api4\Generic\AbstractAction {

  /**
   * Prefix to add to every random value.
   *
   * We define this parameter just by declaring this variable. It will appear in the _API Explorer_,
   * and a getter/setter are magically provided: `$this->setPrefix()` and `$this->getPrefix()`.
   *
   * Declaring this variable with a value (in this case the empty string `''`), sets the default.
   *
   * @var string
   */
  protected $prefix = '';

  /**
   * Number of rows to generate.
   *
   * We can make a parameter required with this annotation:
   * @required
   *
   * We can also require a certain type of input with this annotation:
   * @var int
   */
  //protected $id;

  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * When using the set of Basic actions, they define _run for you and you just need to provide a getter/setter function.
   *
   * @param Result $result
   */
  public function _run(Result $result) {
    
    /**
     * This will be a scheduled job running once a day.
     * It should look for recurring contributions exactly one month ago and then duplicate them
     * However there are unequal days in each month so
     * If for instance it is Feb 28th it should search for recurring donations on
     * 28th, 29th, 30th and 31st Jan
     * So code needs to check if it is the last day of the month and then compare to the
     * previous month to see what is the correct date range to search
     * 31st March would not need to do anything
     *
     */
    
    $today = "2025-05-01";
    //$today = date('Y-m-d');
    $thisYear = date('Y', strtotime($today));
    $thisMonth = date('m', strtotime($today));
    $thisDay = date('d', strtotime($today));
    $lastDay = $thisDay;
    
    $lastMonth = ($thisMonth == 1) ? 12 : $thisMonth - 1;
    $lastMonth = str_pad($lastMonth,2,'0',STR_PAD_LEFT);
    $lastYear = ($thisMonth == 1) ? $thisYear - 1 : $thisYear;
    
    $daysInThisMonth = cal_days_in_month(CAL_GREGORIAN, $thisMonth, $thisYear);
    $daysInLastMonth = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $lastYear);
    
    $startDate = $lastYear . '-' . $lastMonth . '-' . $lastDay;
    $startDate = date("Y-m-d", strtotime($startDate));
    $endDate = $startDate;
    $startDatePlusOne = date('Y-m-d', strtotime($startDate . ' +1 day'));
    // Is this going to be a date range?
    $range = FALSE;
    
    if($thisDay == $daysInThisMonth) {
      if($daysInThisMonth < $daysInLastMonth) {
        $range = TRUE;
        $startDay = $thisDay;
        $endDay = str_pad($daysInLastMonth,2,'0',STR_PAD_LEFT);
        $endDate = $lastYear . '-' . $lastMonth . '-' . $endDay;
      }
    }
    
    //\Civi::log()->debug('Contents of $someInterestingVariable: ' . print_r($startDate . ' ' . $endDate, TRUE));

    if($range) {
      //$api = '{"select":["*"],"where":[["receive_date",">=","' . $startDate . ' 00:00:00"],["receive_date","<=","' . $endDate . ' 23:59:00"]],"limit":25}';
      $contributions = \Civi\Api4\Contribution::get(FALSE)
                                              ->addSelect('*', 'custom.*')
                                              ->addWhere('receive_date', '>=', $startDate . ' 00:00:00')
                                              ->addWhere('receive_date', '<=', $endDate . ' 23:59:00')
                                              ->addWhere('financial_type_id', '=', 1)
                                              //->addWhere('Kin_Contributions.Frequency', '=', 1)
                                              ->execute();
    } else {
      //$api = '{"select":["*"],"where":[["receive_date","=","' . $startDate . '"]],"limit":25}';
      $contributions = \Civi\Api4\Contribution::get(FALSE)
                                              ->addSelect('*', 'custom.*')
                                              ->addWhere('receive_date', '=', $startDate)
                                              ->addWhere('financial_type_id', '=', 1)
                                              //->addWhere('Kin_Contributions.Frequency', '=', 1)
                                              ->execute();
    }
    //\Civi::log()->debug('Contents of $someInterestingVariable: ' . print_r($contributions, TRUE));
    $newContributions = array();
    
    foreach ($contributions as $contribution) {

        /**
         * Check to see that there are no other recurring donations that have been created in the previous month
         * ie, recurring donations that have been created already after the one from a month ago with the same reference
         * If there are, we should not be creating more
         */

        $checkContribution = \Civi\Api4\Contribution::get(FALSE)
            ->selectRowCount()
            ->addWhere('Unique_Contribution_ID.Unique_Contribution_Reference', '=', $contribution["Unique_Contribution_ID.Unique_Contribution_Reference"])
            ->addWhere('receive_date', '>=', $startDatePlusOne . ' 00:00:00')
            ->addWhere('receive_date', '<=', $today . ' 23:59:00')
            ->execute();

        if($checkContribution->rowCount == 0) {
            $newContributions[] = $this->createContributions($contribution);
        }
    }
    
    //$recurringContributions =

      //return $newContribution;

      $result[] = $newContributions;
      
  }
  
  /**
   * @return int
   */
  public function createContributions($originalContribution) {
    //return $this->id;
    
    // Prepare data for the new contribution
    $newContributionData = $originalContribution;
    
    unset($newContributionData['id']); // Remove ID to create a new record
    
    $newContributionData['receive_date'] = date('Y-m-d');
    
    // Generate new unique invoice ID
    $string = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32); // 32 characters, without /=+
    $newContributionData['invoice_id'] = $string;
    
    // Set contribution status to pending (2)
    $newContributionData['contribution_status_id'] = 2;
    
    // Create the new contribution
    $newContribution = civicrm_api4('Contribution', 'create', [
      'values' => $newContributionData,
      'checkPermissions' => FALSE,
    ]);
    
    return $newContribution;
  }

  /**
   * Declare ad-hoc field list for this action.
   *
   * Some actions return entirely different data to the entity's "regular" fields.
   *
   * This is a convenient alternative to adding special logic to our GetFields function to handle this action.
   *
   * @return array
   */
  public static function fields() {

    return [
      ['name' => 'row', 'data_type' => 'Integer'],
      ['name' => 'random'],
    ];

      // Retrieve the original contribution
      /*
      $originalContribution = civicrm_api4('Contribution', 'get', [
          'select' => ['*'],
          'where' => [['id', '=', $rows]],
      ])->first();
      return $originalContribution;
      */



  }
  
  /**
   * @param $contributions
   *
   * @return void
   */
  public function doSomething( $contributions ) {
    $name = $contributions->name;
  }

}
