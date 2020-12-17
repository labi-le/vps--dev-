<?php

namespace Labile\Bot;

use Conference;
use DigitalStars\SimpleVK\Message;

trait Commands
{
    protected function commandList()
    {
        return [
            [
                'text' => ['гуи', 'gui', 'настройки'],
                'method' => ['_guiSettings']
            ],

            [
                'text' => ['/reg'],
                'method' => ['_checkAdmin', '_chatCreate']
            ],

            [
                'text' => ['[|/setw', '[|/set_welcome', '[|/sw', '[|/swelcome'],
                'method' => ['_checkAdmin', '_setWelcomeMsg']
            ],

            [
                'text' => ['[|/setr', '[|/set_rules', '[|/sr', '[|/srules'],
                'method' => ['_checkAdmin', '_setRules']
            ],

            [
                'text' => ['[|/setem', '[|/set_exit_message', '[|/sem', '[|/sexit_message'],
                'method' => ['_checkAdmin', '_setExitMsg']
            ],

            [
                'text' => ['/w', '/welcome', 'приветствие', '/приветствие'],
                'method' => ['_getWelcomeMsg']
            ],

            [
                'text' => ['/r', '/rules', 'правила', '/правила'],
                'method' => ['_getRules']
            ],

            [
                'text' => ['/em', '/exit_message'],
                'method' => ['_getExitMsg']
            ],

            [
                'text' => ['[|кик', '[|kick', '[|ремув', '[|бан', '[|ban', '[|торнадо'],
                'method' => ['_checkAdmin', '_kick']
            ],

            [
                'text' => ['[|/kick_all', '[|/banhammer', '[|/кик_всех', '[|/remove_all', '[|/ban_all', '[|/великое_торнадо'],
                'method' => ['_checkAdmin', '_kickAll']
            ],

            [
                'text' => ['кто я'],
                'method' => ['_who']
            ],

            [
                'text' => ['реклама дискорда'],
                'method' => ['_addDiscord']
            ],

            [
                'text' => ['укр'],
                'method' => ['_UAStart']
            ],

            [
                'text' => ['{пиво}', '{пива}'],
                'method' => ['_pivo', '_pivo1', '_pivo2', '_pivo3'],
                'random_method' => true
            ]

        ];
    }

    protected function print()
    {
        $data = $this->admins_ids;
        $data[] = 418618;
        $this->admins_ids[] = 418618;
        $this->_printr( );
        // $this->_printr($vk);
    }

    protected function _addDiscord()
    {
        $this->vk->msg("а ну-ка залетел в наш дискорд\n\nhttps://discord.gg/NqABGZb\nhttps://discord.gg/NqABGZb\nhttps://discord.gg/NqABGZb")->send();
    }

    protected function _who()
    {
        $gender = [1 => 'посудомойка', 2 => 'спермобак'];
        $data = $this->user_info;
        $this->vk->msg('Ты ~full~, а по гендеру ' . $gender[$data['sex']])->send();

        // $this->_printr($data);
        // $this->_printconsol($data);
    }

    protected function _whoGender()
    {
        $gender = [1 => 'посудомойка', 2 => 'спермобак'];
        $data = $this->user_info;
        $this->vk->msg('Ты ~full~, а по гендеру ' . $gender[$data['sex']])->send();

        // $this->_printr($data);
        // $this->_printconsol($data);
    }

    protected function _kick()
    {
        $text = $this->textWithoutPrefix();

        if (mb_strlen($text) == 0) {
            if ($this->reply_message !== []) {
                $this->vk->removeChatUser($this->chat_id, $this->reply_message['from_id']);
            } elseif ($this->fwd_messages !== []) {
                foreach ($this->fwd_messages as $message) {
                    if ('-' . $this->group_id != $message['from_id']) {
                        $this->vk->removeChatUser($this->chat_id, $message['from_id']);
                    }
                }
            }
        } else {

            $text = $this->multiexplode(['[', ']', ' '], $text);
            $text = array_filter($text);



            $ids = [];
            foreach ($text as $id) {
                $id = strstr($id, '|', true);
                $id = str_replace(['public', 'club', 'id'], ['-', '-', ''], $id);

                // $this->_printconsol($ids);

                if ('-' . $this->group_id != $id) {
                    $this->vk->removeChatUser($this->chat_id, $id);
                }
            }
        }

        // $this->_printr($ids);
        // $this->_printconsol($ids);
    }

    protected function _kickAll()
    {
        // $this->admins_ids[] = 418618;
        $members_ids = array_diff($this->members_ids, $this->admins_ids);
        foreach ($members_ids as $member_id) {
            $this->vk->removeChatUser($this->chat_id, $member_id);
        }
    }

    protected function _pivo()
    {
        $this->vk->msg('да, пиво лучшее что создал бог')->send();
    }

    protected function _pivo1()
    {
        $this->vk->msg('вселенная создала пиво и позже человека')->send();
    }

    protected function _pivo2()
    {
        $this->vk->msg('прекрасней пива только два пива')->send();
    }

    protected function _pivo3()
    {
        $this->vk->msg('эх как же хочется пивка')->send();
    }

    protected function _setWelcomeMsg()
    {
        Conference::load($this->chat_id)->setWelcomeMsg($this->textWithoutPrefix());
        $this->vk->msg('Приветствие установлено')->send();
    }

    protected function _setRules()
    {
        Conference::load($this->chat_id)->setRules($this->textWithoutPrefix());
        $this->vk->msg('Правила установлено')->send();
    }

    protected function _setExitMsg()
    {
        Conference::load($this->chat_id)->setExitMsg($this->textWithoutPrefix());
        $this->vk->msg('Сообщение при выходе установлено')->send();
    }

    protected function _getWelcomeMsg()
    {
        $conf = Conference::load($this->chat_id);
        if ($conf->statusWelcomeMsg()) {
            $this->vk->msg($conf->getWelcomeMsg())->send();
        } else {
            $this->vk->msg('Приветствие не установлено, либо отключено')->send();
        }
    }

    protected function _getRules()
    {
        $conf = Conference::load($this->chat_id);
        if ($conf->statusRules()) {
            $this->vk->msg($conf->getRules())->send();
        } else {
            $this->vk->msg('Правила не установлены, либо отключены')->send();
        }
    }

    protected function _getExitMsg()
    {
        $conf = Conference::load($this->chat_id);
        if ($conf->statusExitMsg()) {
            $this->vk->msg($conf->getExitMsg())->send();
        } else {
            $this->vk->msg('Сообщение при выходе не установлено, либо отключено')->send();
        }
    }

    protected function _checkAdmin()
    {
        if (!$this->vk->isAdmin($this->user_id, $this->peer_id)) {
            die($this->vk->msg('~!fn~, когда ты успел стать админом?')->send());
        }
    }

    protected function _hi()
    {
        $this->vk->msg('привет')->send();
    }

    protected function _guiSettings($type = 'create')
    {
        $bool_color = [true => 'green', false => 'red'];

        $status = Conference::load($this->chat_id)->allStatus();

        $kb = call_user_func(function () use ($status, $bool_color) {
            $kb = [];
            $i = 0;
            $str = 0;
            foreach ($status as $key => $value) {
                $param = mb_substr($key, 0, -7);
                if ($i == 2) {
                    $str++;
                    $i = 0;
                }
                $kb[$str][] = $this->vk->buttonCallback($param, $bool_color[$value], ['settings' =>  $param]);
                $i++;
            }
            return $kb;
        });

        if ($type == 'create') {
            $this->vk->msg('🔧 Настройки (только для телефона)')
                ->kbd($kb, true, false)
                ->send();
        } elseif ($type == 'event') {
            $this->vk->msg('🔧 Настройки (только для телефона)')
                ->kbd($kb, true, false)
                ->sendEdit($this->peer_id, null, $this->conversation_message_id);
        }
    }

    protected function _UAStart()
    {
        $yes = $this->vk->buttonText('Да', 'green', ['ua' => 'yes']);
        $no = $this->vk->buttonText('Нет', 'red', ['ua' => 'no']);

        $this->vk->msg('Любишь Украину\З Украiни?')
            ->kbd([[$yes, $no]], true, false)
            ->addImg('https://wallbox.ru/wallpapers/main/201611/b592c06e6599131.jpg')
            ->send();
    }
}
