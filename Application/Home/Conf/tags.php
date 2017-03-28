<?php
return array(
    // 'app_end'=>array('Home\\Behaviors\\BrowseBehavior'), // 在app_end 标签位添加Test行为
    // 'action_begin'=>array('Home\\Behaviors\\VisitorNewsBehavior'),
    'view_filter' => array('Behavior\\TokenBuildBehavior')
);