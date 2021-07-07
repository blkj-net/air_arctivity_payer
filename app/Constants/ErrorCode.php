<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    /**
     * @Message("successful")
     */
    const SUCCESS = 0;
    /**
     * @Message("操作失败")
     */
    const OPERATE_ERROR = 1000;
    /**
     * @Message("添加失败")
     */
    const CREATE_ERROR = 1001;
    /**
     * @Message("删除失败")
     */
    const DELETE_ERROR = 1002;
    /**
     * @Message("更新失败")
     */
    const UPDATE_ERROR = 1003;
    /**
     * @Message("导入失败")
     */
    const IMPORT_ERROR = 1004;
    /**
     * @Message("缺少参数")
     */
    const PARAMS_LOSE = 1005;
    /**
     * @Message("无效的参数")
     */
    const PARAMS_INVALID = 1006;
    /**
     * @Message("名称已存在")
     */
    const NAME_ALREADY_EXISTS = 1007;



}
