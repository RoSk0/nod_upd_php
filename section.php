<?php
// секции, которые могут быть в базе для V2
$section1_V2= array(
	'ENGINE0'=>1,
	'ENGINE1'=>1,
	'ARCHS0'=>1,
	'ENGINE2'=>1,
	'ARCHS1'=>1,
	'ARCHS2'=>1,
	'ADVHEUR0'=>1,
	'ADVHEUR1'=>1,
	'PWSCAN0'=>1,
	'PWSCAN1'=>1,
	'PWSCAN2'=>1,
	'UTILMOD0'=>1,
	'UTILMOD1'=>1,
	'UTILMOD2'=>1,
	'HELPER0'=>1,
	'HELPER1'=>1,
	'HELPER2'=>1,
	'CHARON0'=>1,
	'CHARON1'=>1,
	'CHARON2'=>1,
	'config000'=>1,
	'ADVHEUR2'=>1,
);

// Секции компонентов,  которые надо оставлять в базе, если они не присутствуют в 
// update.ver для V2
//Аглийсеие версии компонентов
$section2_V2_en= array(
	'NOD32MOD_WINNT_ENGLISH_BASE'=>1,
	'NOD32MOD_WIN98_ENGLISH_BASE'=>1,
	'NOD32MOD_WINNT_ENGLISH_STANDARD'=>1,
	'NOD32MOD_WIN98_ENGLISH_STANDARD'=>1,
	'NOD32MOD_WINNT_ENGLISH_INET'=>1,
	'NOD32MOD_WIN98_ENGLISH_INET'=>1,
	'NOD32MOD_WINNT_ENGLISH_ADMIN'=>1,
	'NOD32MOD_WIN98_ENGLISH_ADMIN'=>1,
	'NOD32MOD_WINNT_ENGLISH_SAK'=>1,
);	
// Русские версии компонентов
$section2_V2_ru= array(
	'NOD32MOD_WINNT_RUSSIAN_BASE'=>1,
	'NOD32MOD_WINNT_RUSSIAN_STANDARD'=>1,
	'NOD32MOD_WINNT_RUSSIAN_INET'=>1,
	'NOD32MOD_WIN98_RUSSIAN_BASE'=>1,
	'NOD32MOD_WIN98_RUSSIAN_STANDARD'=>1,
	'NOD32MOD_WIN98_RUSSIAN_INET'=>1,
	'NOD32MOD_WINNT_RUSSIAN_ADMIN'=>1,
	'NOD32MOD_WIN98_RUSSIAN_ADMIN'=>1,

);

// Секции, которые могут быть в базе для V3,V4
$section1_V3= array(
//Virus Signature Database
	'ENGINE0'=>1,
	'ENGINE1'=>1,
	'ENGINE2'=>1,
//Advanced Heuristics Module
	'ADVHEUR0'=>1,
	'ADVHEUR1'=>1,
	'ADVHEUR2'=>1,
//File System Protection Module
	'AMON0'=>1,
	'AMON1'=>1,
	'AMON2'=>1,
//File System Protection x64  Module
	'AMON640'=>1,
	'AMON641'=>1,
	'AMON642'=>1,
//Anti-Stealth Support Module
	'ANTISTEALTH0'=>1,
	'ANTISTEALTH1'=>1,
	'ANTISTEALTH2'=>1,
//Anti-Stealth Support x64  Module
	'ANTISTEALTH640'=>1,
	'ANTISTEALTH641'=>1,
	'ANTISTEALTH642'=>1,
// Arhive Support Module
	'ARCHIVES0'=>1,
	'ARCHIVES1'=>1,
	'ARCHIVES2'=>1,
// Cleaner Module
	'CLEANER0'=>1,
	'CLEANER1'=>1,
	'CLEANER2'=>1,
//Personal Firewall Module
	'EPFW0'=>1,
	'EPFW1'=>1,
	'EPFW2'=>1,
//Personal Firewall x64  Module
	'EPFW640'=>1,
	'EPFW641'=>1,
	'EPFW642'=>1,
// Update module
	'LOADER0'=>1,
	'LOADER1'=>1,
	'LOADER2'=>1,
// Update x64  module
	'LOADER640'=>1,
	'LOADER641'=>1,
	'LOADER642'=>1,
//Mail Server Module
	'MAILSERVER0'=>1,
	'MAILSERVER1'=>1,
	'MAILSERVER2'=>1,
// Antivirus Scanner Module
	'PERSEUS0'=>1,
	'PERSEUS1'=>1,
	'PERSEUS2'=>1,
//Self-Defense Support Module
	'SELFDEFENSE0'=>1,
	'SELFDEFENSE1'=>1,
	'SELFDEFENSE2'=>1,
//Self-Defense Support x64  Module
	'SELFDEFENSE640'=>1,
	'SELFDEFENSE641'=>1,
	'SELFDEFENSE642'=>1,
//SysInspector Module
	'SYSTEMSTATUS0'=>1,
	'SYSTEMSTATUS1'=>1,
	'SYSTEMSTATUS2'=>1,
//SysInspector x64 Module
	'SYSTEMSTATUS640'=>1,
	'SYSTEMSTATUS641'=>1,
	'SYSTEMSTATUS642'=>1,
//Antispam Module
	'SMON0'=>1,
	'SMON1'=>1,
	'SMON2'=>1,
//Remout Administrator Module
	'RA0'=>1,
	'RA1'=>1,
	'RA2'=>1,
	
);
// Секции, которые могут быть в базе для V5
$section1_V5= array(
//Virus Signature Database
	'ENGINE0'=>1,
	'ENGINE1'=>1,
	'ENGINE2'=>1,
//Advanced Heuristics Module
	'ADVHEUR0'=>1,
	'ADVHEUR1'=>1,
	'ADVHEUR2'=>1,
//File System Protection Module
	'AMON0'=>1,
	'AMON1'=>1,
	'AMON2'=>1,
//File System Protection x64  Module
	'AMON640'=>1,
	'AMON641'=>1,
	'AMON642'=>1,
//Anti-Stealth Support Module
	'ANTISTEALTH0'=>1,
	'ANTISTEALTH1'=>1,
	'ANTISTEALTH2'=>1,
//Anti-Stealth Support x64  Module
	'ANTISTEALTH640'=>1,
	'ANTISTEALTH641'=>1,
	'ANTISTEALTH642'=>1,
// Arhive Support Module
	'ARCHIVES0'=>1,
	'ARCHIVES1'=>1,
	'ARCHIVES2'=>1,
// Cleaner Module
	'CLEANER0'=>1,
	'CLEANER1'=>1,
	'CLEANER2'=>1,
//Personal Firewall Module
	'EPFW0'=>1,
	'EPFW1'=>1,
	'EPFW2'=>1,
//Personal Firewall x64  Module
	'EPFW640'=>1,
	'EPFW641'=>1,
	'EPFW642'=>1,
// Update module
	'LOADER0'=>1,
	'LOADER1'=>1,
	'LOADER2'=>1,
// Update x64  module
	'LOADER640'=>1,
	'LOADER641'=>1,
	'LOADER642'=>1,
//Mail Server Module
	'MAILSERVER0'=>1,
	'MAILSERVER1'=>1,
	'MAILSERVER2'=>1,
// Antivirus Scanner Module
	'PERSEUS0'=>1,
	'PERSEUS1'=>1,
	'PERSEUS2'=>1,
//Self-Defense Support Module
	'SELFDEFENSE0'=>1,
	'SELFDEFENSE1'=>1,
	'SELFDEFENSE2'=>1,
//Self-Defense Support x64  Module
	'SELFDEFENSE640'=>1,
	'SELFDEFENSE641'=>1,
	'SELFDEFENSE642'=>1,
//SysInspector Module
	'SYSTEMSTATUS0'=>1,
	'SYSTEMSTATUS1'=>1,
	'SYSTEMSTATUS2'=>1,
//SysInspector x64 Module
	'SYSTEMSTATUS640'=>1,
	'SYSTEMSTATUS641'=>1,
	'SYSTEMSTATUS642'=>1,
//Antispam Module
	'SMON0'=>1,
	'SMON1'=>1,
	'SMON2'=>1,
//Remout Administrator Module
	'RA0'=>1,
	'RA1'=>1,
	'RA2'=>1,
//Новые модули V5
//Translation support module
	'TRANSLATOR0'=>1,
	'TRANSLATOR1'=>1,
	'TRANSLATOR2'=>1,
//Translation support X64  module	
	'TRANSLATOR640'=>1,
	'TRANSLATOR641'=>1,
	'TRANSLATOR642'=>1,
//HIPS support module
	'HIPS0'=>1,
	'HIPS1'=>1,
	'HIPS2'=>1,
//HIPS support X64  module
	'HIPS640'=>1,
	'HIPS641'=>1,
	'HIPS642'=>1,
//Internet Protection module
	'PROTOSCAN0'=>1,
	'PROTOSCAN1'=>1,
	'PROTOSCAN2'=>1,
//Web Connect Filter Module
	'PARENTAL0'=>1,
	'PARENTAL1'=>1,
	'PARENTAL2'=>1,
//Advanst Antispam Module
	'HORUS0'=>1,
	'HORUS1'=>1,
	'HORUS2'=>1,
//Database module
	'DB0'=>1,
	'DB1'=>1,
	'DB2'=>1,
	
);

// Секции, компонентов, которые надо оставлять в базе, если они не присутствуют в 
// update.ver для V3
// Английские версии компонентов
$section2_V3_en= array(
	'EAV_WINNT32_1033'=>1,
	'EAV_WINNT64_1033'=>1,
	'EAVBE_WINNT32_1033'=>1,
	'EAVBE_WINNT64_1033'=>1,
	'ESS_WINNT32_1033'=>1,
	'ESS_WINNT64_1033'=>1,
	'ESSBE_WINNT32_1033'=>1,
	'ESSBE_WINNT64_1033'=>1,
);	
$section2_V3_ru= array(	
	// Русские версии компонентов
	'EAV_WINNT32_1049'=>1,
	'EAV_WINNT64_1049'=>1,
	'EAVBE_WINNT32_1049'=>1,
	'EAVBE_WINNT64_1049'=>1,
	'ESS_WINNT32_1049'=>1,
	'ESS_WINNT64_1049'=>1,
	'ESSBE_WINNT32_1049'=>1,
	'ESSBE_WINNT64_1049'=>1,

);

// Секции, компонентов, которые надо оставлять в базе, если они не присутствуют в 
// update.ver для V3
// Английские версии компонентов
$section2_V5_en= array(
	'EAV_WINNT32_1033'=>1,
	'EAV_WINNT64_1033'=>1,
	'EAVBE_WINNT32_1033'=>1,
	'EAVBE_WINNT64_1033'=>1,
	'ESS_WINNT32_1033'=>1,
	'ESS_WINNT64_1033'=>1,
	'ESSBE_WINNT32_1033'=>1,
	'ESSBE_WINNT64_1033'=>1,
);	
$section2_V5_ru= array(	
	// Русские версии компонентов
	'EAV_WINNT32_1049'=>1,
	'EAV_WINNT64_1049'=>1,
	'EAVBE_WINNT32_1049'=>1,
	'EAVBE_WINNT64_1049'=>1,
	'ESS_WINNT32_1049'=>1,
	'ESS_WINNT64_1049'=>1,
	'ESSBE_WINNT32_1049'=>1,
	'ESSBE_WINNT64_1049'=>1,

);

?>