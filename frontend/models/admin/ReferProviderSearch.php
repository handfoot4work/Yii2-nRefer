<?php

namespace app\models\admin;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\admin\ReferProvider;

/**
 * ReferProviderSearch represents the model behind the search form about `app\models\admin\ReferProvider`.
 */
class ReferProviderSearch extends ReferProvider
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['prov', 'region', 'provider', 'date_register', 'date_expire', 'usage_group', 'api_key', 'secret_key', 'secret_default', 'hashing', 'responder', 'tel', 'lastkeychange', 'lastlogin', 'remark', 'lastupdate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReferProvider::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date_register' => $this->date_register,
            'date_expire' => $this->date_expire,
            'lastkeychange' => $this->lastkeychange,
            'lastlogin' => $this->lastlogin,
            'lastupdate' => $this->lastupdate,
        ]);

        $query->andFilterWhere(['like', 'prov', $this->prov])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'provider', $this->provider])
            ->andFilterWhere(['like', 'usage_group', $this->usage_group])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'secret_key', $this->secret_key])
            ->andFilterWhere(['like', 'secret_default', $this->secret_default])
            ->andFilterWhere(['like', 'hashing', $this->hashing])
            ->andFilterWhere(['like', 'responder', $this->responder])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
