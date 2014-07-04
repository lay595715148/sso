<?php
namespace sso\test;

use lay\action\TypicalAction;
use lay\util\Logger;

class TestAction extends TypicalAction {
    /**
     * TestService
     * @var TestService
     */
    protected $testService;
    public function onCreate() {
        Logger::debug('onCreate');
        $this->testService = $this->service('sso\test\TestService');
        parent::onCreate();
    }
    public function onGet() {
        $ret = $this->testService->upd(100, array('id' => 100, 'name' => 'test', 'value' => 'redis'));
        Logger::debug($ret);
        $ret = $this->testService->get(100);
        Logger::debug($ret);
        $this->template->push('test', $ret);
    }
}