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
class Random extends \Civi\Api4\Generic\AbstractAction {

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
  protected $rows;

  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * When using the set of Basic actions, they define _run for you and you just need to provide a getter/setter function.
   *
   * @param Result $result
   */
  public function _run(Result $result) {
    for ($i = 1; $i <= $this->rows; $i++) {
      $result[] = [
        'row' => $i,
        'random' => $this->prefix . rand(),
      ];
    }
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
  }

}
