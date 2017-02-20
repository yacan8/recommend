<?php
	namespace PHPSTORM_META {
	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [

		\D('') => [
			'News' instanceof Home\Model\NewsModel,
			'Mongo' instanceof Think\Model\MongoModel,
			'View' instanceof Think\Model\ViewModel,
			'Visitor' instanceof Admin\Model\VisitorModel,
			'Message' instanceof Home\Model\MessageModel,
			'NewsKeywordBelong' instanceof Admin\Model\NewsKeywordBelongModel,
			'Browse' instanceof Home\Model\BrowseModel,
			'Label' instanceof Home\Model\LabelModel,
			'Report' instanceof Home\Model\ReportModel,
			'Adv' instanceof Think\Model\AdvModel,
			'Collection' instanceof Home\Model\CollectionModel,
			'Type' instanceof Home\Model\TypeModel,
			'Relation' instanceof Think\Model\RelationModel,
			'VisitorNews' instanceof Home\Model\VisitorNewsModel,
			'User' instanceof Home\Model\UserModel,
			'Login' instanceof Home\Model\LoginModel,
			'Follow' instanceof Home\Model\FollowModel,
			'Merge' instanceof Think\Model\MergeModel,
			'Sections' instanceof Home\Model\SectionsModel,
			'NewsLabel' instanceof Admin\Model\NewsLabelModel,
			'Comment' instanceof Home\Model\CommentModel,
		],
	];
}