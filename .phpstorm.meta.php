<?php
	namespace PHPSTORM_META {
	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [

		\D('') => [
			'Mongo' instanceof Think\Model\MongoModel,
			'Portrayal' instanceof Admin\Model\PortrayalModel,
			'Apply' instanceof Home\Model\ApplyModel,
			'NewsKeywordBelong' instanceof Home\Model\NewsKeywordBelongModel,
			'Similarity' instanceof Home\Model\SimilarityModel,
			'Dynamics' instanceof Home\Model\DynamicsModel,
			'Relation' instanceof Think\Model\RelationModel,
			'User' instanceof Home\Model\UserModel,
			'Login' instanceof Home\Model\LoginModel,
			'Interest' instanceof Home\Model\InterestModel,
			'Comment' instanceof Home\Model\CommentModel,
			'News' instanceof Home\Model\NewsModel,
			'View' instanceof Think\Model\ViewModel,
			'Visitor' instanceof Admin\Model\VisitorModel,
			'Message' instanceof Home\Model\MessageModel,
			'CancelFollow' instanceof Home\Model\CancelFollowModel,
			'Browse' instanceof Home\Model\BrowseModel,
			'Label' instanceof Home\Model\LabelModel,
			'RecommonedConfig' instanceof Admin\Model\RecommonedConfigModel,
			'Adv' instanceof Think\Model\AdvModel,
			'Collection' instanceof Home\Model\CollectionModel,
			'Type' instanceof Home\Model\TypeModel,
			'VisitorNews' instanceof Home\Model\VisitorNewsModel,
			'Follow' instanceof Home\Model\FollowModel,
			'Zan' instanceof Home\Model\ZanModel,
			'Merge' instanceof Think\Model\MergeModel,
			'Sections' instanceof Admin\Model\SectionsModel,
		],
	];
}