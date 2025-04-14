<?php

namespace Civi\Api4\Action\Kinrc;

/**
 * Example Create action.
 *
 * We could have simply implemented this by returning a BasicCreateAction from our Example entity, passing in 'writeApi4exampleRecord',
 * just like we did with Update, Save, etc. But for demonstration purposes, this is the other way to do it.
 *
 * Here we override the `writeRecord` method. By default that function simply invokes `$this->setter`,
 * but by overriding this class we don't need to pass in a setter and can do the work directly in `writeRecord`.
 *
 * Our writeRecord/setter function takes an array of the record to create. It is responsible for storing the record and,
 * since we did not mark the `id` field "required" in getFields, it must generate a new `id` if not supplied.
 * It should return the created record as an array, including the `id`.
 *
 * Note: One reason to use a callback function rather than putting the save logic directly in `$this->writeRecord`
 * is because the callback can be reused between Create/Update/Save actions, which all use the exact same `writeRecord` function.
 *
 * Read more about how to implement your own `Create` action:
 * @see \Civi\Api4\Generic\BasicCreateAction
 *
 * @package Civi\Api4\Action\Example
 */
class Create extends \Civi\Api4\Generic\BasicCreateAction {

  /**
   * We can override the default value of params, or add docblocks to them,
   * by redeclaring them in our action override.
   *
   * For the parent's docblock contents to appear in the _API Explorer_ as well,
   * we add the `@inheritDoc` annotation and get this:
   * @inheritDoc
   */
  protected $debug = TRUE;

  /**
   * Override writeRecord.
   *
   * This example is a little silly because the parent function would have done exactly the same thing had we passed
   * `'writeApiKincopyRecord'` into this action's constructor. But if you do need to override that method
   * instead of just invoking a callback, this is how to do it.
   *
   * @see BasicCreateAction::writeRecord
   *
   * @param array $item
   * @return array
   */
  protected function writeRecord($item) {
    return writeApiKincopyRecord($item, $this);
  }

}
