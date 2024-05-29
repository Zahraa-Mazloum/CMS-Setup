<?php

namespace app\migrations;

use app\components\inherits\db\Migration;

class M230725133838AddBaseTables extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->unique(),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string()->unique(),
            'status' => $this->enum('status', ['enabled', 'disabled', 'pending']),
            'type' => $this->enum('type', ['developer', 'master', 'admin']),
        ]);

        $this->createTable('admin', [
            'id' => $this->foreignKey('admin.id', 'user.id', type: 'manualPrimaryKey'),
            'name' => $this->string(),
        ]);

        $this->createTable('session', [
            'id' => $this->primaryKey(),
            'user_id' => $this->foreignKey('session.user_id', 'user.id'),
            'token' => $this->string(500)->unique(),
            'os' => $this->string(),
            'browser' => $this->string(),
            'device' => $this->string(),
            'ip' => $this->string(),
            'start_time' => $this->dateTime(),
            'active' => $this->boolean(),
        ]);

        $this->createTable('content', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->defaultValue('default'),
            'status' => $this->enum('status', ['active', 'draft', 'pending', 'deleted']),
            'position' => $this->integer(),
            'attrs' => $this->json(),
        ]);

        $this->createTable('content_local', [
            'id' => $this->primaryKey(),
            'content_id' => $this->foreignKey('content_local.content_id', 'content.id'),
            'language' => $this->string(),
            'title' => $this->string(),
            'excerpt' => $this->text(),
            'blocks' => $this->json(),
            'plain_text' => $this->longText(),
            'attrs' => $this->json(),
        ]);

        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->defaultValue('default'),
            'parent_id' => $this->foreignKey('category.parent_id', 'category.id'),
            'path' => $this->string(),
            'status' => $this->enum('status', ['active', 'draft', 'pending', 'deleted']),
            'position' => $this->integer(),
            'attrs' => $this->json(),
        ]);

        $this->createTable('category_local', [
            'id' => $this->primaryKey(),
            'category_id' => $this->foreignKey('category_local.category_id', 'category.id'),
            'language' => $this->string(),
            'title' => $this->string(),
            'excerpt' => $this->text(),
            'attrs' => $this->json(),
        ]);

        $this->createTable('content_category', [
            'id' => $this->primaryKey(),
            'content_id' => $this->foreignKey('content_category.content_id', 'content.id'),
            'category_id' => $this->foreignKey('content_category.category_id', 'category.id'),
        ]);

        $this->createTable('tag', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->createTable('setting', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique(),
            'value' => $this->text(),
            'type' => $this->enum('type', ['system', 'reference', 'user']),
        ]);

        $this->createRelations();

        return true;
    }

    public function safeDown()
    {
        echo "M230725133838AddBaseTables cannot be reverted.\n";

        return false;
    }
}
