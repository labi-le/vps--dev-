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
                'method' => ['_chatCreate']
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
                'text' => ['{пиво}', '{пива}'],
                'method' => ['_pivo'],
            ],

            [
                'text' => ['{слива}'],
                'method' => ['_lyudaSliva'],
            ],

            [
                'text' => ['скороговорка'],
                'method' => ['_lyudaSkorogovorki'],
            ],

            [
                'text' => ['[|cat', '[|котиков', '[|котикаф', '[|кица'],
                'method' => ['_cat']
            ],

            [
                'text' => ['{😁}', '{😄}', '{😂}', '{🤣}', '{хаха}'],
                'method' => ['_lyudaSmeh'],
            ],

            [
                'text' => ['{🥺}', '{😍}', '{😘}', '{🥰}', '{😻}', '{☺}'],
                'method' => ['_lyudaVanil'],
            ],


        ];
    }


    protected function print()
    {

        // $this->vk->msg()->img($cat)->send();
        $this->_printr(['color' =>  'switch', 'bool' => true]);
        // $this->_printr($vk);
    }

    protected function _cat()
    {
        $count = intval($this->textWithoutPrefix());


        if ($count > 10 or $count <= 0) {
            $this->vk->msg("Отинь многа котикаф либо их ваще нет!!!")->send();
        } else {

            $cat = [];
            $smile = \str_repeat('🐈', $count);

            for ($i = 0; $i < $count; $i++) {
                $cat[] = json_decode(file_get_contents('https://aws.random.cat/meow'));
            }

            $this->vk->msg($smile)->img($cat)->send();
        }
    }

    protected function _addDiscord()
    {
        $this->vk->msg("а ну-ка залетел в наш дискорд\n\nhttps://discord.gg/NqABGZb\nhttps://discord.gg/NqABGZb\nhttps://discord.gg/NqABGZb")->send();
    }

    protected function марков()
    {
        //маша пиши
    }

    protected function _who()
    {
        $gender = [1 => 'посудомойка', 2 => 'спермобак'];
        $data = $this->user_info;
        $this->vk->msg('Ты ~full~, а по гендеру ' . $gender[$data['sex']])->send();
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
            if (!is_null($this->reply_message)) {
                $this->vk->removeChatUser($this->chat_id, $this->reply_message['from_id']);
            } elseif (!is_null($this->fwd_messages)) {
                foreach ($this->fwd_messages as $message) {
                    if ('-' . $this->group_id != $message['from_id']) {
                        $this->vk->removeChatUser($this->chat_id, $message['from_id']);
                    }
                }
            }
        } else {

            $text = $this->multiexplode(['[', ']', ' '], $text);
            $text = array_filter($text);

            foreach ($text as $id) {
                $id = strstr($id, '|', true);
                $id = str_replace(['public', 'club', 'id'], ['-', '-', ''], $id);

                if ('-' . $this->group_id != $id) {
                    $this->vk->removeChatUser($this->chat_id, $id);
                }
            }
        }
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
        $messages = [
            'да, пиво лучшее что создал бог',
            'да, пиво лучшее что создал бог',
            'прекрасней пива только два пива',
            'эх как же хочется пивка'
        ];

        $this->vk->msg(array_rand(array_flip($messages)))->send();
    }

    protected function _lyudaSliva()
    {
        $voices = [
            'https://psv4.userapi.com/c853020//u386342313/audiomsg/d17/dba02f45b4.mp3',
            'https://psv4.userapi.com/c853020//u386342313/audiomsg/d14/7aecca1a10.mp3',
            'https://psv4.userapi.com/c852732//u386342313/audiomsg/d18/302848620e.mp3',
            'https://psv4.userapi.com/c852636//u386342313/audiomsg/d14/35f9592f4c.mp3',
            '',
            '',
        ];
        $this->vk->msg()
            ->voice(array_rand(array_flip($voices)))
            ->send();
    }

    protected function _lyudaSmeh()
    {
        $this->vk->msg()
            ->voice('https://psv4.userapi.com/c852436//u386342313/audiomsg/d5/25da11c48d.mp3')
            ->send();
    }

    protected function _lyudaVanil()
    {
        $this->vk->msg()
            ->voice('https://psv4.userapi.com/c852416//u386342313/audiomsg/d6/a8e16a7cda.mp3')
            ->send();
    }

    protected function _lyudaSkorogovorki()
    {
        $voices = [
            'https://psv4.userapi.com/c852620//u386342313/audiomsg/d4/63331c98c5.mp3',
            'https://psv4.userapi.com/c852724//u386342313/audiomsg/d6/0a2c6da665.mp3',
            'https://psv4.userapi.com/c852628//u386342313/audiomsg/d16/ccba467d60.mp3',
            'https://psv4.userapi.com/c852732//u386342313/audiomsg/d1/70a7339186.mp3',
            'https://psv4.userapi.com/c852528//u386342313/audiomsg/d8/07fde97e2e.mp3',
            'https://psv4.userapi.com/c205420//u386342313/audiomsg/d1/9c73a5a8a0.mp3',
            'https://psv4.userapi.com/c852428//u386342313/audiomsg/d17/99a10b0ab6.mp3',
            'https://psv4.userapi.com/c852724//u386342313/audiomsg/d18/3b5b8ac5ab.mp3',
            'https://psv4.userapi.com/c852536//u386342313/audiomsg/d17/f89fbd1712.mp3',
            'https://psv4.userapi.com/c852520//u386342313/audiomsg/d2/c1ec545da1.mp3',
            'https://psv4.userapi.com/c852624//u386342313/audiomsg/d17/d319771972.mp3',
            '',
        ];
        $this->vk->msg()
            ->voice(array_rand(array_flip($voices)))
            ->send();
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
            $this->vk->msg('~!fn~, когда ты успел стать админом?')->send();
            die();
        }
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
}
