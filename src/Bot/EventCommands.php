<?php

namespace Labile\Bot;

use Conference;
use Timer;

trait EventCommands
{

    protected function eventList()
    {
        return [

            'command' => [
                [
                    'key' => 'not_supported_button',
                    'method' => ['_not_supported_button']
                ]
            ],

            'settings' =>
            [
                [
                    'key' => 'exit_msg',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'welcome_msg',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'rules',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'auto_kick',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'anti_ukraina',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'gender_intolerance',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'anti_spam',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => 'turing_test',
                    'method' => ['_eventCheckAdmin', '_chatSwitcher']
                ],

                [
                    'key' => '->',
                    'method' => ['_poka']
                ]
            ],

            'chat' =>
            [
                [
                    'key' => 'registration',
                    'method' => ['_chatCreate']
                ],

                [
                    'key' => 'push_button',
                    'method' => ['_check_sum']
                ],


            ],

            'ua' =>
            [
                [
                    'key' => 'yes',
                    'method' => ['_sceneUA']
                ],

                [
                    'key' => 'no',
                    'method' => ['_sceneUA']
                ]
            ],

        ];
    }

    protected function _check_sum()
    {
        $timer = Timer::load($this->user_id);
        $timer->set_result_num($this->payload['sum']);
        if ($timer->test_passed() == 'off') {
            if (!$timer->turing()) {
                $timer->remove();
                $this->vk->msg('~Ты~ не прошёл тест, пока-пока')->send();
                $this->vk->removeChatUser($this->chat_id, $this->user_id);
            } elseif ($timer->turing()) {
                $this->vk->msg('Васап ниггер, тест пройден')->send();
                $timer->on_test_passed();
            }
        } else {
            $timer->remove();
            $this->vk->msg('Не твои кнопки вот и не лезь!')->send();
        }
    }

    protected function _chatCreate()
    {
        if ($this->vk->isAdmin('-' . $this->group_id, $this->peer_id)) {
            $this->vk->msg('Ну всё я теперь королева этой конференции поклоняйтесь мне или бан 😡')->send();
            Conference::load($this->chat_id);
        } else {
            $this->vk->msg('Эй ~ты~ кусок ебаного дерьма, где моя чёртова админка?')->send();
        }
    }

    protected function _chatSwitcher()
    {
        Conference::load($this->chat_id)->switcher($this->payload['settings'] . '_status');
        $this->_guiSettings('event');
    }

    protected function _eventCheckAdmin()
    {
        if (!$this->vk->isAdmin($this->user_id, $this->peer_id)) {
            die($this->vk->eventAnswerSnackbar('А когда ты успел стать администратором??'));
        }
    }

    protected function _cbHi()
    {
        $this->vk->eventAnswerSnackbar('Привет');
    }

    protected function _suicide()
    {
        $this->vk->removeChatUser($this->chat_id, $this->user_id);
    }

    protected function _sceneUA()
    {
        if ($this->payload['ua'] == 'yes') {
            $this->vk->msg('Ах ти ебучий хохол пішов нахуй звідси піндос свинка 😡')->send();
            $this->vk->removeChatUser($this->chat_id, $this->user_id);
        } else {
            $this->vk->msg('Фу тупа русня як же я тебе ненавиджу 😑😑🇺🇦')->send();
        }
    }

    protected function _not_supported_button()
    {
        $this->vk->msg('Эти кнопки можно нажимать только с телефона!')->send();
    }

    protected function _printr($data)
    {
        // $id = $this->vk->request('messages.getByConversationMessageId', ['peer_id' => $this->peer_id, 'conversation_message_ids' => $this->conversation_message_id]);
        // $h = $this->vk->request('messages.getHistory', ['start_message_id' => -1, 'count' => 1, 'peer_id' => $this->peer_id]);
        $msg = $this->vk->msg(print_r($data, true))->send();
        // $this->_printconsol($h);
    }

    public function _printconsol($data)
    {
        file_put_contents(
            "php://stdout",
            popen('clear', 'w')
        );
        file_put_contents("php://stdout", print_r($data, true) . "\n");
    }
}
