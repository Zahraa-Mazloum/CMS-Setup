<?php

namespace app\components\inherits\rest;

use yii\rest\Serializer as RestSerializer;

class Serializer extends RestSerializer
{

    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            if (!isset($result[$name])) {
                $result[$name] = [];
            }
            $result[$name][] = $message;
        }

        return error('Data Validation Failed', $result, false, 422);
    }
    
    protected function serializeDataProvider($dataProvider)
    {

        if ($this->preserveKeys) {
            $models = $dataProvider->getModels();
        } else {
            $models = array_values($dataProvider->getModels());
        }
        $models = $this->serializeModels($models);

        if (($pagination = $dataProvider->getPagination()) !== false) {
            $this->addPaginationHeaders($pagination);
        }
        if ($this->request->getIsHead()) {
            return null;
        }

        if ($this->collectionEnvelope === null) {
            return $models;
        }

        $result = [
            $this->collectionEnvelope => $models,
        ];
        if ($pagination !== false) {
            return array_merge($result, $this->serializePagination($pagination));
        }

        return $result;
    }
}
