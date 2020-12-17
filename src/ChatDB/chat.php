<?php

require_once 'db.php';

/**
 * db query
 */
trait dbQuery
{
    public $table;
    public $id;
    public $column;
    public $data;

    public function _printconsol($data)
    {
        file_put_contents(
            "php://stdout",
            popen('clear', 'w')
        );
        file_put_contents("php://stdout", print_r($data, true) . "\n");
    }

    private function checkDB($id)
    {
        if (!$this->isCreate($id)) {
            $res = $this->id = $this->create($id);
        } else {
            $this->id = $this->find($id)->id;
        }
    }

    public static function load($id)
    {
        return new self($id);
    }

    public function getAll()
    {
        return R::getAll('SELECT * FROM ' . $this->table);
    }

    public function count()
    {
        return R::count($this->table);
    }

    public function find($id)
    {
        return R::findOne($this->table, $this->column . ' = ?', [$id]) ?? false;
    }

    private function isCreate($id)
    {
        return $this->find($id);
    }

    public function remove()
    {
        R::trash($this->table, $this->id);
    }

    public function switcher($select)
    {
        $switch = $this->getter($select);
        $this->setter($select, !$switch);
    }

    private function get()
    {
        return R::load($this->table, $this->id);
    }


    private function getter($where)
    {
        return $this->get()->$where;
    }

    private function setter($where, $what)
    {
        $chat = $this->get($this->table, $this->id);
        $chat->$where = $what;
        R::store($chat);
    }
}

class Conference
{
    use dbQuery;

    // public $table;
    // public $id;
    // public $column;
    // public $data;

    public function __construct($id)
    {
        $this->table = 'chats';
        $this->column = 'chat_id';

        $this->checkDB($id);
    }

    private function create($id, $welcome_msg = 'Привет, как делишки у моей малышки?', $exit_msg = 'Либераху порвало новичком')
    {
        $chat = R::Dispense($this->table);


        $chat->chat_id = $id;

        $chat->welcome_msg = $welcome_msg;
        $chat->welcome_msg_status = false;

        $chat->exit_msg = $exit_msg;
        $chat->exit_msg_status = false;

        $chat->rules = false;
        $chat->rules_status = false;

        $chat->auto_kick_status = false;
        $chat->turing_test_status = false;
        $chat->anti_spam_status = false;

        return R::store($chat);
    }

    public function setWelcomeMsg($msg)
    {
        $this->setter('welcome_msg', $msg);
        return $this;
    }

    public function getWelcomeMsg()
    {
        return $this->getter('welcome_msg');
    }

    public function setExitMsg($msg)
    {
        $this->setter('exit_msg', $msg);
        return $this;
    }

    public function getExitMsg()
    {
        return $this->getter('exit_msg');
    }

    public function setRules($rules)
    {
        $this->setter('rules', $rules);
        return $this;
    }

    public function getMessagesCount()
    {
        return $this->getter('messages_count');
    }

    public function messagesCountAutoincrement()
    {
        $this->setter('messages_count', self::getMessagesCount() + 1);
    }

    public function getRules()
    {
        return $this->getter('rules');
    }

    public function statusAutoKick()
    {
        return $this->getter('auto_kick_status');
    }

    public function statusExitMsg()
    {
        return $this->getter('exit_msg_status');
    }

    public function switchAutoKick()
    {
        $this->switcher('auto_kick_status');
        return $this;
    }

    public function switchWelcomeMsg()
    {
        $this->switcher('welcome_msg_status');
        return $this;
    }

    public function switchRules()
    {
        $this->switcher('rules_status');
        return $this;
    }

    public function switchAntiUkraina()
    {
        $this->switcher('anti_ukraina_status');
        return $this;
    }

    public function statusWelcomeMsg()
    {
        return boolval($this->getter('welcome_msg_status') and $this->getter('welcome_msg'));
    }

    public function statusRules()
    {
        return boolval($this->getter('rules_status') and $this->getter('rules'));
    }

    public function statusAntiUkraina()
    {
        return boolval($this->getter('anti_ukraina_status'));
    }

    public function statusGenderIntolerance()
    {
        return boolval($this->getter('gender_intolerance_status'));
    }

    public function statusTuring_test()
    {
        return boolval($this->getter('turing_test_status'));
    }

    public function allStatus()
    {
        $data = $this->get()->getProperties();

        return call_user_func(function () use ($data) {
            $status = [];
            foreach ($data as $key => $value) {
                if (mb_strpos($key, '_status')) {
                    $status[$key] = $value;
                }
            }
            return $status;
        });
    }
}


class Timer
{
    use dbQuery;
    // private $table = 'timer';
    // public $id;
    // private $column = 'user_id';

    public function __construct($id)
    {
        $this->table = 'timer';
        $this->column = 'user_id';

        $this->checkDB($id);
    }

    private function create($id)
    {
        $chat = R::dispense($this->table);
        $chat->user_id = $id;
        $chat->valid_num = 0;
        $chat->result_num = 0;
        $chat->test_passed = null;

        return R::store($chat);
    }

    public function set_valid_num($int)
    {
        $this->setter('valid_num', $int);
    }

    public function set_result_num($int)
    {
        $this->setter('result_num', $int);
    }

    public function valid_num()
    {
        return $this->getter('valid_num');
    }

    public function result_num()
    {
        return $this->getter('result_num');
    }

    public function turing()
    {
        if ($this->valid_num() == $this->result_num()) {
            return true;
        } else return false;
    }

    public function test_passed()
    {
        return $this->getter('test_passed');
    }

    public function on_test_passed()
    {
        $this->setter('test_passed', 'on');
    }

    public function off_test_passed()
    {
        $this->setter('test_passed', 'off');
    }
}
