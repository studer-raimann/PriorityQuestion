<#1>
<?php
$ilDB = \srag\DIC\DICStatic::dic()->database();
$res = $ilDB->queryF('SELECT * FROM svy_qtype WHERE type_tag = %s', array( 'text' ), array( 'PriorityQuestion' ));
if ($res->numRows() == 0) {
	$res = $ilDB->query('SELECT MAX(questiontype_id) maxid FROM svy_qtype');
	$data = $ilDB->fetchAssoc($res);
	$max = $data['maxid'] + 1;

	$affectedRows = $ilDB->manipulateF('INSERT INTO svy_qtype (questiontype_id, type_tag, plugin) VALUES (%s, %s, %s)', array(
			'integer',
			'text',
			'integer'
		), array(
			$max,
			'PriorityQuestion',
			1
		));
}
?>
<#2>
<?php
$fields = array(
	'question_fi' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'num_prios' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	),
	'ranked_prios' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	)
);
$ilDB = \srag\DIC\DICStatic::dic()->database();
if(!$ilDB->tableExists('spl_svyq_prioq_prioq')) {
	$ilDB->createTable("spl_svyq_prioq_prioq", $fields);
	$ilDB->addPrimaryKey("spl_svyq_prioq_prioq", array( "question_fi" ));
}
?>
<#3>
<?php
$fields = array(
	'question_fi' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'prio' => array(
		'type' => 'text',
		'length' => 120,
		'notnull' => false
	)
);
$ilDB = \srag\DIC\DICStatic::dic()->database();
if(!$ilDB->tableExists('spl_svyq_prioq_prios')) {
	$ilDB->createTable("spl_svyq_prioq_prios", $fields);
}
?>
<#4>
<?php
$fields = array(
	'answer_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'priority' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'priority_text' => array(
		'type' => 'text',
		'length' => 120,
		'notnull' => true
	)
);
$ilDB = \srag\DIC\DICStatic::dic()->database();
if(!$ilDB->tableExists('spl_svyq_prioq_pria')) {
	$ilDB->createTable("spl_svyq_prioq_pria", $fields);
}
?>
<#5>
<?php
$ilDB = \srag\DIC\DICStatic::dic()->database();
if(!$ilDB->tableColumnExists('spl_svyq_prioq_prios', 'ordernumber')) {
	$ilDB->addTableColumn('spl_svyq_prioq_prios', 'ordernumber', array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	));
}
?>
<#6>
<?php
$ilDB = \srag\DIC\DICStatic::dic()->database();
if(!$ilDB->tableColumnExists('spl_svyq_prioq_pria', 'question_fi')) {
	$ilDB->addTableColumn('spl_svyq_prioq_pria', 'question_fi', array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	));
}

if(!$ilDB->tableColumnExists('spl_svyq_prioq_pria', 'active_fi')) {
	$ilDB->addTableColumn('spl_svyq_prioq_pria', 'active_fi', array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	));
}
?>
<#7>
<?php
$sql = "SELECT spl_svyq_prioq_pria.answer_id, svy_answer.question_fi, svy_answer.active_fi, svy_finished.finished_id
FROM ilias.spl_svyq_prioq_pria
INNER join svy_answer on svy_answer.answer_id = spl_svyq_prioq_pria.answer_id
INNER join svy_finished on svy_finished.finished_id = svy_answer.active_fi
where svy_answer.answer_id is not null
group by spl_svyq_prioq_pria.answer_id, svy_answer.question_fi, svy_answer.active_fi, svy_finished.finished_id
";
$ilDB = \srag\DIC\DICStatic::dic()->database();
$result = $ilDB->query($sql);
while ($row = $ilDB->fetchAssoc($result)) {
	$sql = "UPDATE spl_svyq_prioq_pria SET question_fi = ".$row['question_fi'].", active_fi =  ".$row['active_fi']." where answer_id = ".$row['answer_id'];
	$ilDB->manipulate($sql);
}
?>