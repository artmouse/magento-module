<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Api\Data;

interface RuleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';

    public const RULE_NAME = 'rule_name';

    public const MOVE_TO_GROUP = 'move_to_group';

    public const PRIORITY = 'priority';

    public const STATUS = 'status';

    public const CONDITIONS_SERIALIZED = 'conditions_serialized';

    /**#@-*/

    /**
     * @return string
     */
    public function getRuleName();

    /**
     * @param string $ruleName
     *
     * @return \Amasty\GroupAssign\Api\Data\RuleInterface
     */
    public function setRuleName($ruleName);

    /**
     * @return int
     */
    public function getMoveToGroup();

    /**
     * @param int $group
     *
     * @return \Amasty\GroupAssign\Api\Data\RuleInterface
     */
    public function setMoveToGroup($group);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return \Amasty\GroupAssign\Api\Data\RuleInterface
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\GroupAssign\Api\Data\RuleInterface
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param int $conditions
     *
     * @return \Amasty\GroupAssign\Api\Data\RuleInterface
     */
    public function setConditionsSerialized($conditions);
}
