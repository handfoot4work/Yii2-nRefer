<?php

namespace app\models\admin;

/**
 * This is the ActiveQuery class for [[ReferProvider]].
 *
 * @see ReferProvider
 */
class ReferQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ReferProvider[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ReferProvider|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
