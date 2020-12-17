<?php

// declare(strict_types=1);

namespace Labile\Bot;

use DigitalStars\SimpleVK\SimpleVK as vk;
use Conference as Conference;
use DigitalStars\SimpleVK\Message;

class Bot
{
    use Utils;
    use CommandController;
    use Chat;
    use ChatEvents;
    use Commands;
    use EventCommands;

    protected $similar_percent;
    protected $vk;
    protected $msg;
    protected $conversation_message_id;
    protected $fwd_messages;


    protected $user_id, $peer_id, $chat_id;
    protected $text, $text_lower;
    protected $data;

    protected $action_member_id;
    protected $group_id;

    protected $payload;
    protected $attachments;
    protected $msg_id;

    protected $user_info;

    protected $members;
    protected $members_ids;
    protected $admins;
    protected $admins_ids;


    public function __construct(string $token, string $version, string $confirmation, int $similar_percent = 75)
    {
        $this->vk = vk::create($token, $version)->setConfirm($confirmation);
        $this->vk->setUserLogError(418618);
        $this->similar_percent = $similar_percent;
    }


    /**
     * Событие message_new
     */
    public function message_new($data): void
    {
        $this->user_info = $this->vk->userInfo($this->user_id, ['sex']);

        $this->conversation_message_id = $data['conversation_message_id'];
        $this->fwd_messages = $data['fwd_messages'] ?? null;
        $this->reply_message = $data['reply_message'] ?? null;

        if (isset($data['action'])) $this->handleAction($data);

        $text_lower = mb_strtolower($this->text);
        $this->text_lower = $text_lower;

        if (method_exists($this, $text_lower) && mb_strpos($text_lower, '_') === false) {
            $this->$text_lower();
        } else {
            $this->commandExecute();
        }
    }

    /**
     * Событие message_event
     */
    public function message_event($data): void
    {
        $this->conversation_message_id = $data['conversation_message_id'];
        $this->eventExecute();
    }

    /**
     * Выполнить действия беседы
     * @param $data
     */
    protected function handleAction(array $data): void
    {
        $action = $data['action'];
        $type = $action['type'];
        $member_id = $action['member_id'];

        $this->action_member_id = $action = $member_id;

        if (method_exists($this, $type)) $this->$type();
    }


    protected function parse($type): void
    {
        switch ($type) {

            case 'message_new':
                $this->loadChatMembers($this->peer_id);
                $data = $this->data['object']['message'];

                if (isset($data['payload'])) {
                    if (!is_array($data['payload']))
                        $this->payload = json_decode($data['payload'], true);

                    $this->eventExecute();
                }

                $this->message_new($data);
                break;

            case 'message_event':
                $this->message_event($this->data['object']);
                break;
        }
    }

    public function run(): void
    {
        $data = $this->vk->initVars($this->peer_id, $this->user_id, $this->type, $this->text, $this->payload, $this->msg_id, $this->attachments);
        $this->data = $data;
        $this->chat_id = ($this->peer_id - 2e9) > 0 ? $this->peer_id - 2e9 : null;
        $this->group_id = $data['group_id'] ?? false;
        $this->parse($this->type);
    }
}
