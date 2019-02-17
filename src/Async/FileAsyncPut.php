<?php
/**
 * Created by IntelliJ IDEA.
 * User: kernel Huang
 * Email: kernelman79@gmail.com
 * Date: 2018/11/24
 * Time: 4:11 PM
 */

namespace Files\Async;


use Exceptions\NotFoundException;

/**
 * 异步文件写入类
 *
 * Class FilePut
 * @package Processor
 */
class FileAsyncPut {

    public $file    = null; // 文件路径
    public $append  = null; // 添加/覆盖
    public $locked  = null; // 锁定/非锁定
    public $content = null; // 写入内容

    /**
     * FilePut constructor.
     *
     * @param $file
     * @param $content
     * @param bool $append
     * @throws NotFoundException
     */
    public function __construct($file, $content, $append = true) {
        // Check swoole extension
        if (!extension_loaded('swoole')) {
            throw new NotFoundException('The swoole extension can not loaded.');
        }

        $this->file     = $file;
        $this->append   = $append;
        $this->content  = $content;
    }

    /**
     * 执行文件写入操作
     *
     * @return bool
     */
    public function run() {
        if ($this->append) {
            return $this->nonLockedAndAppend();
        }

        return $this->nonLockedAndNonAppend();
    }

    /**
     * 追加文件内容
     *
     * @return bool|int
     */
    private function nonLockedAndAppend() {
        if ($this->append) {
            return \Swoole\Async::writeFile($this->file, $this->content, null, FILE_APPEND);
        }

        return false;
    }

    /**
     * 非添加（直接覆写文件）
     *
     * @return bool|int
     */
    private function nonLockedAndNonAppend() {
        if (!$this->append) {
            return \Swoole\Async::writeFile($this->file, $this->content, null);
        }

        return false;
    }

    /**
     * 释放内存
     */
    public function __destruct() {
        unset($this->file);
        unset($this->append);
        unset($this->content);
    }
}
