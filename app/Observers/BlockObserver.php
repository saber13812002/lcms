<?php
/**
 * Laravel-cms - cms based on laravel
 *
 * @category  Laravel-cms
 * @package   Laravel
 * @author    Qiangzi <35924784@qq.com>
 * @date      2018/06/06 09:08:00
 * @copyright Copyright 2018 CMS
 * @license   https://opensource.org/licenses/MIT
 * @github    https://github.com/35924784/laravel-cms
 ***
 * @version   Release 1.0
 */

namespace App\Observers;

use App\Models\Block;
use Illuminate\Support\Facades\Auth;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

/**
 * 区块观察者
 *
 * Class BlockObserver
 * @package App\Observers
 */
class BlockObserver
{
    public function creating(Block $block)
    {
        $block->object_id || $block->object_id = create_object_id();
        $block->created_op || $block->created_op = Auth::id();
        $block->updated_op || $block->updated_op = Auth::id();
    }

    public function updating(Block $block)
    {
        $block->updated_op = Auth::id();
    }
    
    public function updated(Block $block){
        Block::clearCache($block->object_id);
    }

    public function saving(Block $block){
        if(is_array($block->content) || is_object($block->content)){
            $block->content = json_encode($block->content, JSON_UNESCAPED_UNICODE);
        }
    }
}