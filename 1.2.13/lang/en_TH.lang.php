<?php
/**
* English (en) translation file.
* This also serves as the base translation file from which to derive
*  all other translations.
*  
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @translator Your Name <your@email.address.net>
* @version 05-07-06
* @package Languages
*
* Copyright (C) 2003 - 2007 phpScheduleIt
* License: GPL, see LICENSE
*/
///////////////////////////////////////////////////////////
// INSTRUCTIONS
///////////////////////////////////////////////////////////
// This file contains all of the strings that are used throughout phpScheduleit.
// Please save the translated file as '2 letter language code'.lang.php.  For example, en.lang.php.
// 
// To make phpScheduleIt available in another language, simply translate each
//  of the following strings into the appropriate one for the language.  If there
//  is no direct translation, please provide the closest translation.  Please be sure
//  to make the proper additions the /config/langs.php file (instructions are in the file).
//  Also, please add a help translation for your language using en.help.php as a base.
//
// You will probably keep all sprintf (%s) tags in their current place.  These tags
//  are there as a substitution placeholder.  Please check the output after translating
//  to be sure that the sentences make sense.
//
// + Please use single quotes ' around all $strings.  If you need to use the ' character, please enter it as \'
// + Please use double quotes " around all $email.  If you need to use the " character, please enter it as \"
//
// + For all $dates please use the PHP strftime() syntax
//    http://us2.php.net/manual/en/function.strftime.php
//
// + Non-intuitive parts of this file will be explained with comments.  If you
//    have any questions, please email lqqkout13@users.sourceforge.net
//    or post questions in the Developers forum on SourceForge
//    http://sourceforge.net/forum/forum.php?forum_id=331297
///////////////////////////////////////////////////////////

////////////////////////////////
/* Do not modify this section */
////////////////////////////////
global $strings;			  //
global $email;				  //
global $dates;				  //
global $charset;			  //
global $letters;			  //
global $days_full;			  //
global $days_abbr;			  //
global $days_two;			  //
global $days_letter;		  //
global $months_full;		  //
global $months_abbr;		  //
global $days_letter;		  //
/******************************/

// Charset for this language
// 'iso-8859-1' will work for most languages
$charset = 'tis-620';

/***
  DAY NAMES
  All of these arrays MUST start with Sunday as the first element 
   and go through the seven day week, ending on Saturday
***/
// The full day name
//$days_full = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$days_full = array("�ѹ�ҷԵ��", "�ѹ�ѹ���", "�ѹ�ѧ���", "�ѹ�ظ", "�ѹ����ʺ��", "�ѹ�ء��", "�ѹ�����");
// The three letter abbreviation
//$days_abbr = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
$days_abbr = array("��.", "�ѹ.", "�ѧ.", "�ظ", "��.", "�ء��", "�����");
// The two letter abbreviation
//$days_two  = array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa');
$days_two  = array("��", "�.", "�.", "�.", "��", "�.", "�.");
// The one letter abbreviation
//$days_letter = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
$days_letter = array("��", "�", "�", "�", "��", "�", "�");

/***
  MONTH NAMES
  All of these arrays MUST start with January as the first element
   and go through the twelve months of the year, ending on December
***/
// The full month name
$months_full = array('���Ҥ�', '����Ҿѹ��', '�չҤ�', '����¹', '����Ҥ�', '�Զع�¹', '�á�Ҥ�', '�ԧ�Ҥ�', '�ѹ��¹', '���Ҥ�', '��ɨԡ�¹', '�ѹ�Ҥ�');

// The three letter month name
$months_abbr = array("�.�.", "�.�.", "��.�.", "��.�.", "�.�.", "��.�.", "�.�.", "�.�.", "�.�.", "�.�.", "�.�.", "�.�.");

// All letters of the alphabet starting with A and ending with Z
$letters = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

/***
  DATE FORMATTING
  All of the date formatting must use the PHP strftime() syntax
  You can include any text/HTML formatting in the translation
***/
// General date formatting used for all date display unless otherwise noted
$dates['general_date'] = '%d/%m/%Y';
// General datetime formatting used for all datetime display unless otherwise noted
// The hour:minute:second will always follow this format
$dates['general_datetime'] = '%d/%m/%Y @';
// Date in the reservation notification popup and email
$dates['res_check'] = '%A %d/%m/%Y';
// Date on the scheduler that appears above the resource links
$dates['schedule_daily'] = '%A,<br/>%d/%m/%Y';
// Date on top-right of each page
$dates['header'] = '%A��� %d %B %Y';
// Jump box format on bottom of the schedule page
// This must only include %m %d %Y in the proper order,
//  other specifiers will be ignored and will corrupt the jump box 
$dates['jumpbox'] = '%d %m %Y';

/***
  STRING TRANSLATIONS
  All of these strings should be translated from the English value (right side of the equals sign) to the new language.
  - Please keep the keys (between the [] brackets) as they are.  The keys will not always be the same as the value.
  - Please keep the sprintf formatting (%s) placeholders where they are unless you are sure it needs to be moved.
  - Please keep the HTML and punctuation as-is unless you know that you want to change it.
***/
$strings['hours'] = '=�������';
$strings['minutes'] = '�ҷ�';
// The common abbreviation to hint that a user should enter the month as 2 digits
$strings['mm'] = 'mm';
// The common abbreviation to hint that a user should enter the day as 2 digits
$strings['dd'] = 'dd';
// The common abbreviation to hint that a user should enter the year as 4 digits
$strings['yyyy'] = 'yyyy';
$strings['am'] = 'am';
$strings['pm'] = 'pm';

$strings['Administrator'] = '�������к�';
$strings['Welcome Back'] = '�Թ��㹡�á�Ѻ�� %s';
$strings['Log Out'] = '�͡�ҡ�к�';
$strings['My Control Panel'] = '���ͧ�Ǻ�����ǹ���';
$strings['Help'] = '���������';
$strings['Manage Schedules'] = '��èѴ���ҧ����';
$strings['Manage Users'] = '��èѴ�����Ҫԡ';
$strings['Manage Resources'] = '�ҹ�����ž�ʴ�';
$strings['Manage User Training'] = 'Manage User Training';
$strings['Manage Reservations'] = '��䢡�èͧ';
$strings['Email Users'] = '��������';
$strings['Export Database Data'] = '���͡������';
$strings['Reset Password'] = '��駤�����ʼ�ҹ����';
$strings['System Administration'] = '�������к�';
$strings['Successful update'] = '��Ѻ��ا�����������';
$strings['Update failed!'] = '��Ѻ��ا��������������!';
$strings['Manage Blackout Times'] = '��èѴ���ҧ�͡����';
$strings['Forgot Password'] = '������ʼ�ҹ';
$strings['Manage My Email Contacts'] = '�Ѵ�����ª���������ͧ�ѹ';
$strings['Choose Date'] = '���͡�ѹ';
$strings['Modify My Profile'] = '����¹�ŧ�����Ţͧ�ѹ';
$strings['Register'] = 'ŧ����¹�����';
$strings['Processing Blackout'] = 'Processing Blackout';
$strings['Processing Reservation'] = '���ѧ�ӡ�èͧ';
$strings['Online Scheduler [Read-only Mode]'] = '���ҧ��èͧ [�������ҧ����]';
$strings['Online Scheduler'] = '���ҧ�ͧ';
$strings['phpScheduleIt Statistics'] = 'ʶԵ���ҹ';
$strings['User Info'] = '�����ż����:';

$strings['Could not determine tool'] = 'Could not determine tool. Please return to My Control Panel and try again later.';
$strings['This is only accessable to the administrator'] = '੾�м��Ѵ����к���������ҹ��';
$strings['Back to My Control Panel'] = '��Ѻ��ѧ˹�ҨͤǺ����ͧ�ѹ';
$strings['That schedule is not available.'] = '�ѧ�������ö��ҹ���ҧ���ҹ����';
$strings['You did not select any schedules to delete.'] = '�س��������͡���ҧ���ҷ���ͧ��è�ź';
$strings['You did not select any members to delete.'] = '�س�ѧ��������͡�����ҹ����ͧ��è�ź';
$strings['You did not select any resources to delete.'] = '�س��������͡��¡������Ѻ�ͧ��ҹ ���͵�ͧ��è�ź';
$strings['Schedule title is required.'] = '���͵��ҧ���Ҩͧ���������¡';
$strings['Invalid start/end times'] = '�������١��ͧ';
$strings['View days is required'] = '��ͧ�����ʴ���Ẻ����ѹ�����¹�';
$strings['Day offset is required'] = '���ӹǹ�ѹ����ͧ���ŧ仴���';
$strings['Admin email is required'] = '������ͧ���Ǻ����к��ѧ�����';
$strings['Resource name is required.'] = '��ͧ���������¡�ͧ��¡�÷���Դ���ͧ��';
$strings['Valid schedule must be selected'] = '�ô���͡���ҧ������ͧ���١��ͧ����';
$strings['Minimum reservation length must be less than or equal to maximum reservation length.'] = '�������ҡ�èͧ���ҧ��ӹ�鹵�ͧ�դ�ҹ��¡���������ҡѺ�������ҷ�����ͧ���٧�ش';
$strings['Your request was processed successfully.'] = '���Թ�������������º���´�';
$strings['Go back to system administration'] = '��Ѻ价����ǹ�Ǻ��������';
$strings['Or wait to be automatically redirected there.'] = '�������ѡ����С�Ѻ��ѧ˹�Ҩ͡�͹˹�ҹ��';
$strings['There were problems processing your request.'] = '����ͼԴ��Ҵ�Դ��鹢�зӡ�û����ż�';
$strings['Please go back and correct any errors.'] = '�ͧ��Ѻ���䢢����������ա����';
$strings['Login to view details and place reservations'] = '�������������к��������ö�ӡ�èͧ��ͧ��';
$strings['Memberid is not available.'] = '���ʼ���� %s ����ѧ�������ö��ҹ��';

$strings['Schedule Title'] = '���͵��ҧ��èͧ';
$strings['Start Time'] = '���������';
$strings['End Time'] = '����ش����';
$strings['Time Span'] = '��������';
$strings['Weekday Start'] = '�ѹ��������ѻ����';
$strings['Admin Email'] = '����������к�';

$strings['Default'] = '����������';
$strings['Reset'] = '����¹���ʼ�ҹ����';
$strings['Edit'] = '���';
$strings['Delete'] = 'ź';
$strings['Cancel'] = '¡��ԡ';
$strings['View'] = '��';
$strings['Modify'] = '���';
$strings['Save'] = '�Ѵ��';
$strings['Back'] = '��Ѻ';
$strings['Next'] = '����';
$strings['Close Window'] = '�Դ˹�ҵ�ҧ';
$strings['Search'] = '����';
$strings['Clear'] = '��ҧ';

$strings['Days to Show'] = '�ѹ����ͧ����ʴ�';
$strings['Reservation Offset'] = '���ҷ���ͧ��èͧ';
$strings['Hidden'] = '��͹';
$strings['Show Summary'] = '�ʴ��Ҿ���';
$strings['Add Schedule'] = '����������ͧ���ҧ��èͧ';
$strings['Edit Schedule'] = '��䢡�������ҧ��èͧ';
$strings['No'] = '���';
$strings['Yes'] = '��';
$strings['Name'] = '�����-���ʡ��';
$strings['First Name'] = '����';
$strings['Last Name'] = '���ʡ��';
$strings['Resource Name'] = '������¡��';
$strings['Email'] = '������';
$strings['Institution'] = '���/�ҹ/˹��§ҹ';
$strings['Phone'] = '���Ѿ��';
$strings['Password'] = '���ʼ�ҹ';
$strings['Permissions'] = '���͹حҵ';
$strings['View information about'] = '�ʴ������Ţͧ %s %s';
$strings['Send email to'] = '������������ %s %s';
$strings['Reset password for'] = '��駤�����ʼ�ҹ��������Ѻ %s %s';
$strings['Edit permissions for'] = '��䢡��͹حҵ�ͧ %s %s';
$strings['Position'] = '���˹�';
$strings['Password (6 char min)'] = '���ʼ�ҹ (%s char min)';	// @since 1.1.0
$strings['Re-Enter Password'] = '������ʼ�ҹ����';

$strings['Sort by descending last name'] = '���§�ӴѺ���ʡ����͹��Ѻ';
$strings['Sort by descending email address'] = '���§�ӴѺ������������͹��Ѻ';
$strings['Sort by descending institution'] = '���§�ӴѺ����˹��§ҹ���ͧҹ��͹��Ѻ';
$strings['Sort by ascending last name'] = '���§�ӴѺ���ʡ��';
$strings['Sort by ascending email address'] = '���§�ӴѺ����������';
$strings['Sort by ascending institution'] = '���§�ӴѺ����˹��§ҹ���ͧҹ';
$strings['Sort by descending resource name'] = '���§�ӴѺ������¡�÷���Դ�ͧ��͹��Ѻ';
$strings['Sort by descending location'] = '���§�ӴѺ���ͪ���ʶҹ�����͹��Ѻ';
$strings['Sort by descending schedule title'] = '���§�ӴѺ���͵��ҧ��èͧ��͹��Ѻ';
$strings['Sort by ascending resource name'] = '���§�ӴѺ������¡�÷���Դ�ͧ';
$strings['Sort by ascending location'] = '���§�ӴѺ���ͪ���ʶҹ���';
$strings['Sort by ascending schedule title'] = '���§�ӴѺ���͵��ҧ��èͧ';
$strings['Sort by descending date'] = '���§�ӴѺ�ѹ������¡����͹��Ѻ';
$strings['Sort by descending user name'] = '���§�ӴѺ������͹��Ѻ';
$strings['Sort by descending start time'] = '���§�ӴѺ����������ͧ��͹��Ѻ';
$strings['Sort by descending end time'] = '���§�ӴѺ��������ش��͹��Ѻ';
$strings['Sort by ascending date'] = '���§�ӴѺ�ѹ������¡��';
$strings['Sort by ascending user name'] = '���§�ӴѺ����';
$strings['Sort by ascending start time'] = '���§�ӴѺ����������ͧ';
$strings['Sort by ascending end time'] = '���§�ӴѺ��������ش';
$strings['Sort by descending created time'] = '���§�ӴѺ���ҷ�����¡����͹��Ѻ';
$strings['Sort by ascending created time'] = '���§�ӴѺ���ҷ�����¡��';
$strings['Sort by descending last modified time'] = '���§�ӴѺ��¡�èͧ�������¹�ŧ��͹��Ѻ';
$strings['Sort by ascending last modified time'] = '���§�ӴѺ��¡�èͧ�������¹�ŧ';

$strings['Search Users'] = '���Ҫ��ͼ����ҹ';
$strings['Location'] = 'ʶҹ��';
$strings['Schedule'] = '���ҧ����';
$strings['Phone'] = '���Ѿ��';
$strings['Notes'] = '��';
$strings['Status'] = 'ʶҹ�';
$strings['All Schedules'] = '���ҧ���ҷ�����';
$strings['All Resources'] = '�����ŷ�Ѿ�ҡ÷�����';
$strings['All Users'] = '����������';

$strings['Edit data for'] = '��䢢����Ţͧ %s';
$strings['Active'] = '�����ҹ';
$strings['Inactive'] = '�ЧѺ�����ҹ';
$strings['Toggle this resource active/inactive'] = '�������͵�駤����ҹ���������ҹ';
$strings['Minimum Reservation Time'] = '�������ҡ�èͧ��鹵��';
$strings['Maximum Reservation Time'] = '�������ҡ�èͧ����٧';
$strings['Auto-assign permission'] = '͹حҵ���ѵ��ѵ�';
$strings['Add Resource'] = '������������ͧ����ö';
$strings['Edit Resource'] = '��䢢�����';
$strings['Allowed'] = '�������';
$strings['Notify user'] = '����͹�����';
$strings['User Reservations'] = '��èͧ�ͧ�����';
$strings['Date'] = '�ѹ���';
$strings['User'] = '�����ҹ';
$strings['Email Users'] = '��������ѧ�����';
$strings['Subject'] = '����ͧ';
$strings['Message'] = '��ͤ���';
$strings['Please select users'] = '���͡�����ҹ';
$strings['Send Email'] = '��������';
$strings['problem sending email'] = '��س��ͧ�������ա����';
$strings['The email sent successfully.'] = '�������������';
$strings['do not refresh page'] = '<u>����</u> �Ѿഷ˹�Ҩ��¡� F5 �������͡ Refresh ���Шз�����ա���������ӫ�͹';
$strings['Return to email management'] = '��Ѻ��ѧ��ǹ�Ѵ���������';
$strings['Please select which tables and fields to export'] = '�ô���͡���ҧ�����������ǵ��ҧ�������͡������:';
$strings['all fields'] = '- ��ǵ��ҧ�����ŷ����� -';
$strings['HTML'] = 'Ẻ˹���Ǻ';
$strings['Plain text'] = '��ͤ���������';
$strings['XML'] = 'XML';
$strings['CSV'] = 'CSV';
$strings['Export Data'] = '���͡������';
$strings['Reset Password for'] = '��駤�����ʼ�ҹ����ͧ %s';
$strings['Please edit your profile'] = '�����䢢�������ǹ���';
$strings['Please register'] = 'ŧ����¹�����ҹ';
$strings['Keep me logged in'] = '����������к���� <br/>(requires cookies)';
$strings['Edit Profile'] = '��䢢�������ǹ���';
$strings['Register'] = 'ŧ����¹';
$strings['Please Log In'] = '��������ͤ�к����ͨͧ��ҹ';
$strings['Email address'] = '������';
$strings['Password'] = '���ʼ�ҹ';
$strings['First time user'] = '�����ҹ�����á?';
$strings['Click here to register'] = 'ŧ����¹���������';
$strings['Register for phpScheduleIt'] = 'ŧ����¹�����������к�';
$strings['Log In'] = '�������к�';
$strings['View Schedule'] = '�ٵ��ҧ��èͧ';
$strings['View a read-only version of the schedule'] = '�ʴ��Ţ����Ţͧ���ҧ���Ҩͧ���ҧ����';
$strings['I Forgot My Password'] = '������ʼ�ҹ';
$strings['Retreive lost password'] = '����������ʼ�ҹ����';
$strings['Get online help'] = '�ͤ������������Ẻ�͹�Ź�';
$strings['Language'] = '����';
$strings['(Default)'] = '(����������)';

$strings['My Announcements'] = '��С�Ȣͧ�ѹ';
$strings['My Reservations'] = '��èͧ�ͧ�ѹ';
$strings['My Permissions'] = '��¡�÷������ö�ӡ�èͧ��';
$strings['My Quick Links'] = '�����Ѵ';
$strings['Announcements as of'] = '��С����ѹ��� %s';
$strings['There are no announcements.'] = '����բ�ͤ�����С����';
$strings['Resource'] = '��¡��';
$strings['Created'] = '�ӡ�èͧ�����';
$strings['Last Modified'] = '�������ش';
$strings['View this reservation'] = '�ʴ���èͧ�ѹ���';
$strings['Modify this reservation'] = '��䢡�èͧ�ѹ���';
$strings['Delete this reservation'] = 'ź��èͧ�ѹ���';
$strings['Bookings'] = '���ҧ����Ѻ�ͧ��ҹ';							// @since 1.2.0
$strings['Change My Profile Information/Password'] = '����¹�ŧ��������ǹ���';		// @since 1.2.0
$strings['Manage My Email Preferences'] = '��䢡����ҹ������';				// @since 1.2.0
$strings['Mass Email Users'] = '����������� ��';
$strings['Search Scheduled Resource Usage'] = '���ҡ�èͧ��ҹ';		// @since 1.2.0
$strings['Export Database Content'] = '���͡�����Ũҡ�ҹ������';
$strings['View System Stats'] = '��ʶҹ��к�';
$strings['Email Administrator'] = '����价��������к�';

$strings['Email me when'] = '�������ҩѹ�����:';
$strings['I place a reservation'] = '�����ŧ���ҧ���ҡ�èͧ�ͧ';
$strings['My reservation is modified'] = '��¡�á�èͧ�ͧ�ѹ��١��Ѻ��ا����';
$strings['My reservation is deleted'] = '��¡�á�èͧ�ͧ�ѹ��١ź����';
$strings['I prefer'] = '��ͧ�������ʴ��� :';
$strings['Your email preferences were successfully saved'] = '��������´������ͧ�س��١�Ѵ������';
$strings['Return to My Control Panel'] = '��Ѻ�˹�ҨͤǺ���';

$strings['Please select the starting and ending times'] = '���͡����������������������ش:';
$strings['Please change the starting and ending times'] = '����¹�ŧ����������������������ش:';
$strings['Reserved time'] = '���ҷ��ͧ:';
$strings['Minimum Reservation Length'] = '��ǧ���ҡ�èͧ����ش:';
$strings['Maximum Reservation Length'] = '��ǧ���ҡ�èͧ�٧�ش:';
$strings['Reserved for'] = '�١�ͧ�������Ѻ:';
$strings['Will be reserved for'] = '�١�ͧ����������:';
$strings['N/A'] = '����բ�����';
$strings['Update all recurring records in group'] = '��Ѻ��ا��¡�÷���������ҡ�㹡����?';
$strings['Delete?'] = 'ź����?';
$strings['Never'] = '-- ������ --';
$strings['Days'] = '�ѹ';
$strings['Weeks'] = '�ѻ����';
$strings['Months (date)'] = '��͹ (�ѹ���)';
$strings['Months (day)'] = '��͹ (�ѹ)';
$strings['First Days'] = '�ѹ�á';
$strings['Second Days'] = '�ѹ����ͧ';
$strings['Third Days'] = '�ѹ������';
$strings['Fourth Days'] = '�ѹ������';
$strings['Last Days'] = '�ѹ�ش����';
$strings['Repeat every'] = '�ͧ��ӷء��ѹ���:';
$strings['Repeat on'] = '�ͧ����ѹ���:';
$strings['Repeat until date'] = '�ͧ仨��֧:';
$strings['Choose Date'] = '���͡�ѹ';
$strings['Summary'] = '��ػ';

$strings['View schedule'] = '���ҧ����Ẻ :';
$strings['My Reservations'] = '��¡�èͧ����·�';
$strings['My Past Reservations'] = '��èͧ�ͧ�ѹ�����ش����';
$strings['Other Reservations'] = '��èͧ����';
$strings['Other Past Reservations'] = '��èͧ���� ����·���';
$strings['Blacked Out Time'] = '��ѧ���ҷӡ��';
$strings['Set blackout times'] = '�����������Ѻ���ҹ͡�ӡ�� %s on %s'; 
$strings['Reserve on'] = '�ͧ %s ��ѹ��� %s';
$strings['Prev Week'] = '&laquo; �ѻ�����͹';
$strings['Jump 1 week back'] = '��¡�Ѻ� 1 �ѻ����';
$strings['Prev days'] = '< %d �ѹ��͹';
$strings['Previous days'] = '&#8249; ��ҹ�� %d �ѹ';
$strings['This Week'] = '�ѻ������';
$strings['Jump to this week'] = '�������ѻ������';
$strings['Next days'] = '%d �ѹ��ҧ˹�� >';
$strings['Next Week'] = '�ѻ����Ѵ� &raquo;';
$strings['Jump To Date'] = '������ѧ�ѹ���';
$strings['View Monthly Calendar'] = '�ʴ���Ẻ�����͹';
$strings['Open up a navigational calendar'] = '�Դ�ٻ�ԷԹ';

$strings['View stats for schedule'] = '��ʶԵԢͧ���ҧ:';
$strings['At A Glance'] = '�������Ҿ���';
$strings['Total Users'] = '����������:';
$strings['Total Resources'] = '��觢ͧ��������ͧ:';
$strings['Total Reservations'] = '�ӹǹ��èͧ������:';
$strings['Max Reservation'] = '��èͧ�٧�ش:';
$strings['Min Reservation'] = '��èͧ����ش:';
$strings['Avg Reservation'] = '��èͧ�����:';
$strings['Most Active Resource'] = '��觷��١�ͧ���ҡ����ش:';
$strings['Most Active User'] = '�������ͧ��ҹ�ҡ����ش:';
$strings['System Stats'] = '�������к�';
$strings['phpScheduleIt version'] = 'phpScheduleIt version:';
$strings['Database backend'] = 'Database backend:';
$strings['Database name'] = 'Database name:';
$strings['PHP version'] = 'PHP version:';
$strings['Server OS'] = 'Server OS:';
$strings['Server name'] = 'Server name:';
$strings['phpScheduleIt root directory'] = 'phpScheduleIt root directory:';
$strings['Using permissions'] = 'Using permissions:';
$strings['Using logging'] = 'Using logging:';
$strings['Log file'] = 'Log file:';
$strings['Admin email address'] = 'Admin email address:';
$strings['Tech email address'] = 'Tech email address:';
$strings['CC email addresses'] = 'CC email addresses:';
$strings['Reservation start time'] = '���ҷ��������鹨ͧ��ҹ:';
$strings['Reservation end time'] = '��������ش��èͧ��ҹ:';
$strings['Days shown at a time'] = 'Days shown at a time:';
$strings['Reservations'] = '��èͧ��ҹ';
$strings['Return to top'] = '��Ѻ仴�ҹ��';
$strings['for'] = '����Ѻ';

$strings['Select Search Criteria'] = '���͡���͹�㹡�ä���';
$strings['Schedules'] = '���ҧ����:';
$strings['All Schedules'] = '������ͧ���ҧ������';
$strings['Hold CTRL to select multiple'] = '������ CTRL ��ҧ����������͡��¡����';
$strings['Users'] = '�����:';
$strings['All Users'] = '����������';
$strings['Resources'] = '��¡�â�����������';		// @since 1.2.0
$strings['All Resources'] = '����¹�ͧ���������';
$strings['Starting Date'] = '�ѹ����������:';
$strings['Ending Date'] = '�ѹ����ش:';
$strings['Starting Time'] = '�����������:';
$strings['Ending Time'] = '����ش�����:';
$strings['Output Type'] = '�ٻẺ����ʴ���:';
$strings['Manage'] = '��èѴ���';
$strings['Total Time'] = '���ҷ�����';
$strings['Total hours'] = '�ӹǹ�������������:';
$strings['% of total resource time'] = '% �ͧ���ҷ�����';
$strings['View these results as'] = 'View these results as:';
$strings['Edit this reservation'] = '��䢢����š�èͧ';
$strings['Search Results'] = '���Ѿ���ä���';
$strings['Search Resource Usage'] = '���ҡ�èͧ��ҹ�ػ�ó�';
$strings['Search Results found'] = '���Ѿ���ä��Ҿ� %d ����';
$strings['Try a different search'] = '�ͧ������Ӥ�����';
$strings['Search Run On'] = '������¡�÷����ѧ��ҹ����:';
$strings['Member ID'] = 'ID �����';
$strings['Previous User'] = '&laquo; �����ҹ����͹';
$strings['Next User'] = '�����ҹ�Ѵ� &raquo;';

$strings['No results'] = '��辺��¡����';
$strings['That record could not be found.'] = '��辺������㹷���¹';
$strings['This blackout is not recurring.'] = '���ҧ�͡�����������ö���������';
$strings['This reservation is not recurring.'] = '�������ö������ҡ�èͧ��ҹ��';
$strings['There are no records in the table.'] = '����բ�����㹵��ҧ %s';
$strings['You do not have any reservations scheduled.'] = '�ѧ����ҡ�����·���¡���ҡ�͹';
$strings['You do not have permission to use any resources.'] = '�ѧ�������ö�ӡ�èͧ��';
$strings['No resources in the database.'] = '��辺��¡�÷��зӡ�èͧ����к�';
$strings['There was an error executing your query'] = '�բ�ͼԴ��Ҵ�Դ��鹢�еԴ��Ͱҹ������:';

$strings['That cookie seems to be invalid'] = 'Cookie ������Ƿ����ҹ';
$strings['We could not find that logon in our database.'] = '�к��������ö������ª���㹰ҹ��������';	// @since 1.1.0
$strings['That password did not match the one in our database.'] = '���ʼ�ҹ������������к�';
$strings['You can try'] = '<br />�س�Ҩ���ͧ<br />ŧ����¹���� email <br />����<br />����ͧ�к������ա����';
$strings['A new user has been added'] = '���ͼ����ҹ����١������������';
$strings['You have successfully registered'] = '�س��ŧ����¹���º��������!';
$strings['Continue'] = '�յ��...';
$strings['Your profile has been successfully updated!'] = '�����Ţͧ�س��١��Ѻ����!';
$strings['Please return to My Control Panel'] = '�ô��Ѻ价��˹�ҵ�ҧ�Ǻ���';
$strings['Valid email address is required.'] = '- ������������������ٳ�';
$strings['First name is required.'] = '- ��͹����������';
$strings['Last name is required.'] = '- ��͹���ʡ��������';
$strings['Phone number is required.'] = '- ��͹�����õԴ��ʹ���';
$strings['That email is taken already.'] = '- �ա����������������<br />�ô��������������';
$strings['Min 6 character password is required.'] = '- �ӹǹ���ҧ��� %s ����ѡ��';
$strings['Passwords do not match.'] = '- ���ʼ�ҹ�������͹�ѹ.';

$strings['Per page'] = '���˹��:';
$strings['Page'] = '˹�ҷ��:';

$strings['Your reservation was successfully created'] = '���Ѻ��¡������';
$strings['Your reservation was successfully modified'] = '��¡�á�èͧ���Ѻ��û�Ѻ������';
$strings['Your reservation was successfully deleted'] = '��¡�á�èͧ��١ź�����';
$strings['Your blackout was successfully created'] = '��¡�á�èͧ�͡���ҷӡ�����Ѻ���������¹��èͧ����';
$strings['Your blackout was successfully modified'] = '��¡�á�èͧ�͡���ҷӡ�����Ѻ��û�Ѻ������';
$strings['Your blackout was successfully deleted'] = '��¡�á�èͧ�͡���ҷӡ����١ź�����';
$strings['for the follwing dates'] = '��ѹ�ѧ���仹��:';
$strings['Start time must be less than end time'] = '����������鹵�ͧ�դ�ҹ��¡�����������ش';
$strings['Current start time is'] = '���ҷ��������ͧ���';
$strings['Current end time is'] = '����ش���Ũͧ�����';
$strings['Reservation length does not fall within this resource\'s allowed length.'] = '�������ҡ�èͧ������ҹ�������㹪�ǧ����˹�';
$strings['Your reservation is'] = '��èͧ��ͧ�س���';
$strings['Minimum reservation length'] = '�������Ҩͧ�����ش���:';
$strings['Maximum reservation length'] = '�������Ҩͧ�ҡ�ش���:';
$strings['You do not have permission to use this resource.'] = '�س������Է���ͧ������Ѻ��觹��';
$strings['reserved or unavailable'] = '%s �֧ %s ��١�ͧ����¡��ԡ�����';	// @since 1.1.0
$strings['Reservation created for'] = 'ŧ����¹��èͧ���� %s';
$strings['Reservation modified for'] = '��䢷���¹��èͧ����Ѻ %s';
$strings['Reservation deleted for'] = 'ź����¹��èͧ�ͧ %s';
$strings['created'] = '���ҧ����';
$strings['modified'] = '�������';
$strings['deleted'] = 'ź����';
$strings['Reservation #'] = '��èͧ #';
$strings['Contact'] = '��õԴ���';
$strings['Reservation created'] = '��¡�èͧ��١���ҧ����';
$strings['Reservation modified'] = '��¡�èͧ��١�������';
$strings['Reservation deleted'] = '��¡�èͧ��١ź�����';

$strings['Reservations by month'] = '���ҧ�ͧ�����͹';
$strings['Reservations by day of the week'] = '���ҧ�ͧ����ѹ�ͧ�����ѻ����';
$strings['Reservations per month'] = '���ҧ��èͧ�����͹';
$strings['Reservations per user'] = '���ҧ��èͧ��ͼ����';
$strings['Reservations per resource'] = '���ҧ��èͧ�����觷�����ͧ';
$strings['Reservations per start time'] = '���ҧ��èͧ������ҷ��������ͧ';
$strings['Reservations per end time'] = '���ҧ��èͧ������ҷ������ش��èͧ';
$strings['[All Reservations]'] = '[All Reservations]';

$strings['Permissions Updated'] = '��䢡��͹حҵ����';
$strings['Your permissions have been updated'] = '%s �ͧ�س���Ѻ����������';
$strings['You now do not have permission to use any resources.'] = '�͹���س������Է���㹡�èͧ����';
$strings['You now have permission to use the following resources'] = '�س����ö�ͧ��ҹ��¡�ôѧ���仹��:';
$strings['Please contact with any questions.'] = '�ô�Դ��� %s ������բ��ʧ���';
$strings['Password Reset'] = '���ʼ�ҹ��١�����������';

$strings['This will change your password to a new, randomly generated one.'] = '������зӡ�����ҧ�������������������Ң��������';
$strings['your new password will be set'] = '�����ѧ�ҡ���س������ ����¹�������� ����������������ʼ�ҹ��ѧ����������س�����������ʼ�ҹ���������к��ա����';
$strings['Change Password'] = '����¹���ʼ�ҹ����';
$strings['Sorry, we could not find that user in the database.'] = '���� ��辺�����������س����ҡ�������к�';
$strings['Your New Password'] = '���������� %s ';
$strings['Your new passsword has been emailed to you.'] = '�����!<br />'
    			. '��������������Ҥس����<br />'
    			. '�ô��Ǩ�����ʼ�ҹ����㹡��ͧ���������Ҩҡ���价��<a href="index.php"> �������к�</a>'
    			. ' �������ʼ�ҹ��� �������ö����¹�ŧ���ʼ�ҹ�����������价�� &quot;Change My Profile Information/Password&quot;';

$strings['You are not logged in!'] = '�س�ѧ������������к�';

$strings['Setup'] = '��õ�駤����к�';
$strings['Please log into your database'] = 'Please log into your database';
$strings['Enter database root username'] = 'Enter database root username:';
$strings['Enter database root password'] = 'Enter database root password:';
$strings['Login to database'] = 'Login to database';
$strings['Root user is not required. Any database user who has permission to create tables is acceptable.'] = 'Root user is <b>not</b> required. Any database user who has permission to create tables is acceptable.';
$strings['This will set up all the necessary databases and tables for phpScheduleIt.'] = 'This will set up all the necessary databases and tables for phpScheduleIt.';
$strings['It also populates any required tables.'] = 'It also populates any required tables.';
$strings['Warning: THIS WILL ERASE ALL DATA IN PREVIOUS phpScheduleIt DATABASES!'] = 'Warning: THIS WILL ERASE ALL DATA IN PREVIOUS phpScheduleIt DATABASES!';
$strings['Not a valid database type in the config.php file.'] = 'Not a valid database type in the config.php file.';
$strings['Database user password is not set in the config.php file.'] = 'Database user password is not set in the config.php file.';
$strings['Database name not set in the config.php file.'] = 'Database name not set in the config.php file.';
$strings['Successfully connected as'] = 'Successfully connected as';
$strings['Create tables'] = 'Create tables &gt;';
$strings['There were errors during the install.'] = 'There were errors during the install. It is possible that phpScheduleIt will still work if the errors were minor.<br/><br/>'
	. 'Please post any questions to the forums on <a href="http://sourceforge.net/forum/?group_id=95547">SourceForge</a>.';
$strings['You have successfully finished setting up phpScheduleIt and are ready to begin using it.'] = 'You have successfully finished setting up phpScheduleIt and are ready to begin using it.';
$strings['Thank you for using phpScheduleIt'] = 'Please be sure to COMPLETELY REMOVE THE \'install\' DIRECTORY.'
	. ' This is critical because it contains database passwords and other sensitive information.'
	. ' Failing to do so leaves the door wide open for anyone to break into your database!'
	. '<br /><br />'
	. 'Thank you for using phpScheduleIt!';
$strings['There is no way to undo this action'] = 'There is no way to undo this action!';
$strings['Click to proceed'] = 'Click to proceed';
$strings['Please delete this file.'] = 'Please delete this file.';
$strings['Successful update'] = 'The update succeeded fully';
$strings['Patch completed successfully'] = 'Patch completed successfully';

// @since 1.0.0 RC1
$strings['If no value is specified, the default password set in the config file will be used.'] = 'If no value is specified, the default password set in the config file will be used.';
$strings['Notify user that password has been changed?'] = 'Notify user that password has been changed?';

// @since 1.1.0
$strings['This system requires that you have an email address.'] = '��ͧ�����������ͧ�س����Ѻ�����ҹ�к����';
$strings['Invalid User Name/Password.'] = '���ͼ����ҹ�������ʼ�ҹ���١��ͧ';
$strings['Pending User Reservations'] = 'Pending User Reservations';
$strings['Approve'] = '͹��ѵ�';
$strings['Approve this reservation'] = '͹��ѵԡ�èͧ���';
$strings['Approve Reservations'] ='͹��ѵ���¡�èͧ';

$strings['Announcement'] = '��С��';
$strings['Number'] = '��蹷��';
$strings['Add Announcement'] = '����С��';
$strings['Edit Announcement'] = '��䢻�С��';
$strings['All Announcements'] = '��С�ȷ�����';
$strings['Delete Announcements'] = 'ź��С��';
$strings['Use start date/time?'] = 'Use start date/time?';
$strings['Use end date/time?'] = 'Use end date/time?';
$strings['Announcement text is required.'] = '��ͧ��â�����ŧ��ҹ㹻�С��';
$strings['Announcement number is required.'] = '�ѧ�������������Ţ���駷����¹/��䢻�Ѻ��ا��С��';

$strings['Pending Approval'] = '�͡��͹��ѵ���ҹ';
$strings['My reservation is approved'] = '��¡�èͧ�ͧ�ѹ���Ѻ͹��ѵ�����';
$strings['This reservation must be approved by the administrator.'] = '��¡�èͧ������Ѻ���͹��ѵ�����';
$strings['Approval Required'] = '��ͧ��á��͹��ѵ�';
$strings['No reservations requiring approval'] = '��辺��¡�èͧ���·���͡��͹��ѵ�';
$strings['Your reservation was successfully approved'] = '��èͧ��ҹ�ͧ�س���Ѻ���͹��ѵ�����';
$strings['Reservation approved for'] = '͹��ѵԡ�èͧ����Ѻ %s';
$strings['approved'] = '��ҹ...!';
$strings['Reservation approved'] = '͹��ѵԡ�èͧ��ҹ';

$strings['Valid username is required'] = '�ô�����ͼ����ҹ���١��ͧ';
$strings['That logon name is taken already.'] = '��������Ѻ�������к���١��ҹ�¼���������';
$strings['this will be your login'] = '(���͹���ͪ�������Ѻ�������к�)';
$strings['Logon name'] = '�������¡�����ҹ';
$strings['Your logon name is'] = 'Your logon name is %s';

$strings['Start'] = '�����';
$strings['End'] = '����ش';
$strings['Start date must be less than or equal to end date'] = '�ѹ���������ӡ�èͧ��ҹ�е�ͧ�դ�ҹ��¡����ѹ�������ش��èͧ��';
$strings['That starting date has already passed'] = '�ѹ���ͧ������ҹ������';
$strings['Basic'] = '��������´�����';
$strings['Participants'] = '��ª��ͼ���������';
$strings['Close'] = '�Դ';
$strings['Start Date'] = '�ѹ����������';
$strings['End Date'] = '�ѹ�������ش';
$strings['Minimum'] = '����ش';
$strings['Maximum'] = '�٧�ش';
$strings['Allow Multiple Day Reservations'] = '����ö���͡�������ѹ';
$strings['Invited Users'] = '��ª��ͼ���������';
$strings['Invite Users'] = 'Invite Users';
$strings['Remove Participants'] = '��Ҫ��ͼ�����������Ъ���͡';
$strings['Reservation Invitation'] = 'Reservation Invitation';
$strings['Manage Invites'] = '�Ѵ��á���ԭ';
$strings['No invite was selected'] = '������ԭ���������';
$strings['reservation accepted'] = '%s ��ͺ�Ѻ�����������ѹ��� %s';
$strings['reservation declined'] = '%s ��¡��ԡ�ͺ�Ѻ�����������ѹ��� %s';
$strings['Login to manage all of your invitiations'] = '�������к����ͨѴ��ä��ԭ';
$strings['Reservation Participation Change'] = 'Reservation Participation Change';
$strings['My Invitations'] = '���ҧ�ͧ������������';
$strings['Accept'] = '��ŧ';
$strings['Decline'] = '¡��ԡ';
$strings['Accept or decline this reservation'] = '����Ѻ����¡��ԡ�������������';
$strings['My Reservation Participation'] = '��¡�èͧ���١�ԭ�������';
$strings['End Participation'] = 'End Participation';
$strings['Owner'] = '������¡��';
$strings['Particpating Users'] = '�����١�ԭ���������Ъ��������ҹ';
$strings['No advanced options available'] = '����յ�����͡���� ����';
$strings['Confirm reservation participation'] = '�ͺ�Ѻ�����������������ԭ';
$strings['Confirm'] = '�׹�ѹ';
$strings['Do for all reservations in the group?'] = 'Do for all reservations in the group?';

$strings['My Calendar'] = '���ҧ�ͧ�ͧ�����';
$strings['View My Calendar'] = '�ٻ�ԷԹ�ͧ�ѹ';
$strings['Participant'] = '��������������';
$strings['Recurring'] = '�������';
$strings['Multiple Day'] = '����� �ѹ';
$strings['[today]'] = '[�ѹ���]';
$strings['Day View'] = '�ʴ�����ѹ';
$strings['Week View'] = '�ʴ�����ѻ����';
$strings['Month View'] = '�ʴ������͹';
$strings['Resource Calendar'] = 'Resource Calendar';
$strings['View Resource Calendar'] = '�ٵ��ҧ��èͧ';	// @since 1.2.0
$strings['Signup View'] = 'Signup View';

$strings['Select User'] = '���͡�ؤ�';
$strings['Change'] = '���';

$strings['Update'] = '��Ѻ��ا������';
$strings['phpScheduleIt Update is only available for versions 1.0.0 or later'] = 'phpScheduleIt Update is only available for versions 1.0.0 or later';
$strings['phpScheduleIt is already up to date'] = 'phpScheduleIt is already up to date';
$strings['Migrating reservations'] = 'Migrating reservations';

$strings['Admin'] = 'Admin';
$strings['Manage Announcements'] = '��û�С�ȵ�ҧ�';
$strings['There are no announcements'] = '�ѧ����բ�ͤ�����С����';
// end since 1.1.0

// @since 1.2.0
$strings['Maximum Participant Capacity'] = '�ӹǹ���٧�ش';
$strings['Leave blank for unlimited'] = '�����ҧ����ҡ���ӡѴ�ӹǹ';
$strings['Maximum of participants'] = '��¡�èͧ����ö�ͧ�Ѻ������������٧�ش %s ��';
$strings['That reservation is at full capacity.'] = '�������';
$strings['Allow registered users to join?'] = '��������Ҫԡ���������?';
$strings['Allow non-registered users to join?'] = '������ؤ���¹͡���������?';
$strings['Join'] = '�������';
$strings['My Participation Options'] = 'My Participation Options';
$strings['Join Reservation'] = 'Join Reservation';
$strings['Join All Recurring'] = 'Join All Recurring';
$strings['You are not participating on the following reservation dates because they are at full capacity.'] = '�س�������ö��������������ҧ�ͧ���Шӹǹ������������֧�ӹǹ�٧�ش����';
$strings['You are already invited to this reservation. Please follow participation instructions previously sent to your email.'] = '�س���������㹵��ҧ����ͧ������� �ô��������ͧ�س������ѧ';
$strings['Additional Tools'] = 'Additional Tools';
$strings['Create User'] = '���ҧ���������';
$strings['Check Availability'] = '��Ǩ�ٵ��ҧ��ҹ';
$strings['Manage Additional Resources'] = '�Ѵ����ػ�ó��Сͺ����';
$strings['Number Available'] = '�ӹǹ';
$strings['Unlimited'] = '���ӡѴ';
$strings['Add Additional Resource'] = '������¡�������������';
$strings['Edit Additional Resource'] = '�����¡�������������';
$strings['Checking'] = '��Ǩ�ͺ';
$strings['You did not select anything to delete.'] = '��������͡��¡���� ���ͷӡ��ź';
$strings['Added Resources'] = 'Added Resources';
$strings['Additional resource is reserved'] = '��¡��������� %s ����§ %s 㹵͹���';
$strings['All Groups'] = '����������ҹ������';
$strings['Group Name'] = '���͡���������ҹ';
$strings['Delete Groups'] = 'ź����������ҹ';
$strings['Manage Groups'] = '����觡���������ҹ';
$strings['None'] = '�����';
$strings['Group name is required.'] = '�ô�����͡���������ҹ';
$strings['Groups'] = '�������Ҫԡ';
$strings['Current Groups'] = '����������ҹ��й��';
$strings['Group Administration'] = 'Group Administration';
$strings['Reminder Subject'] = '�к���͹��èͧ- %s, %s %s';
$strings['Reminder'] = 'Reminder';
$strings['before reservation'] = 'before reservation';
$strings['My Participation'] = '���������Ъ���ͧ�ѹ';
$strings['My Past Participation'] = '����������������Ъ������ҹ��';
$strings['Timezone'] = 'ࢵ���� (�������ҷ����ҧ�ҡ����ͧ Server)';
$strings['Export'] = '���͡';
$strings['Select reservations to export'] = '���͡��¡�÷������͡';
$strings['Export Format'] = 'Export Format';
$strings['This resource cannot be reserved less than x hours in advance'] = 'This resource cannot be reserved less than %s hours in advance';
$strings['This resource cannot be reserved more than x hours in advance'] = 'This resource cannot be reserved more than %s hours in advance';
$strings['Minimum Booking Notice'] = 'Minimum Booking Notice';
$strings['Maximum Booking Notice'] = 'Maximum Booking Notice';
$strings['hours prior to the start time'] = 'hours prior to the start time';
$strings['hours from the current time'] = 'hours from the current time';
$strings['Contains'] = 'Contains';
$strings['Begins with'] = 'Begins with';
$strings['Minimum booking notice is required.'] = 'Minimum booking notice is required.';
$strings['Maximum booking notice is required.'] = 'Maximum booking notice is required.';
$strings['Accessory Name'] = 'Accessory Name';
$strings['Accessories'] = 'Accessories';
$strings['All Accessories'] = 'All Accessories';
$strings['Added Accessories'] = 'Added Accessories';
// end since 1.2.0

/***
  EMAIL MESSAGES
  Please translate these email messages into your language.  You should keep the sprintf (%s) placeholders
   in their current position unless you know you need to move them.
  All email messages should be surrounded by double quotes "
  Each email message will be described below.
***/
// @since 1.1.0
// Email message that a user gets after they register
$email['register'] = "%s, %s \r\n"
				. "�س��ŧ����¹���º��������������������´�ѧ���:\r\n"
				. "������͡�Թ: %s\r\n"
				. "����-���ʡ��: %s %s \r\n"
				. "���Ѿ��: %s \r\n"
				. "ʶҹ���ӧҹ: %s \r\n"
				. "���˹�: %s \r\n\r\n"
				. "�ô�������к����:\r\n"
				. "%s \r\n\r\n"
				. "�س����ö���ԧ��价���èͧ�������������´�ͧ�س�����ͧ�����ǹ���\r\n\r\n"
				. "�ջѭ���ô�Դ��� %s";

// Email message the admin gets after a new user registers
$email['register_admin'] = "Administrator,\r\n\r\n"
					. "A new user has registered with the following information:\r\n"
					. "Email: %s \r\n"
					. "Name: %s %s \r\n"
					. "Phone: %s \r\n"
					. "Institution: %s \r\n"
					. "Position: %s \r\n\r\n";

// First part of the email that a user gets after they create/modify/delete a reservation
// 'reservation_activity_1' through 'reservation_activity_6' are all part of one email message
//  that needs to be assembled depending on different options.  Please translate all of them.
// @since 1.1.0
$email['reservation_activity_1'] = "%s,\r\n<br />"
			. "�س�����¡�� %s ��èͧ�� #%s \r\n\r\n<br/><br/>"
			. "�����ʡ�èͧ��������͵�ͧ��õԴ��ͼ��Ǻ����к� \r\n\r\n<br/><br/>"
			. "��èͧ��ҹ�����ҧ���� %s %s �֧ %s %s �ͧ %s"
			. " ��� %s ��١ %s.\r\n\r\n<br/><br/>";
$email['reservation_activity_2'] = "��èͧ����㹪�ǧ�������� �ͧ�ء�ѹ��� \r\n<br/>";
$email['reservation_activity_3'] = "��¡�èͧ���� �������Ǣ�ͧ�Ѻ��èͧ��� %s.\r\n\r\n<br/><br/>";
$email['reservation_activity_4'] = "�����˵ \r\n<br/>%s\r\n\r\n<br/><br/>";
$email['reservation_activity_5'] = "�ҡ�բ�ͼԴ��Ҵ�ô�Դ��ͼ����ŷ�� %s"
			. " �����õԴ��� %s.\r\n\r\n<br/><br/>"
			. "�س����ö����������¡�èͧ�ѹ�����"
			. " ���� %s ��� \r\n<br/>"
			. "<a href=\"%s\" target=\"_blank\">%s</a>.\r\n\r\n<br/><br/>";
$email['reservation_activity_6'] = "�ҡ�ջѭ�� �ô�Դ���价�� <a href=\"mailto:%s\">%s</a>.\r\n\r\n<br/><br/>";
// @since 1.1.0
$email['reservation_activity_7'] = "%s,\r\n<br />"
			. "��èͧ #%s ��١͹��ѵ����� \r\n\r\n<br/><br/>"
			. "��������ʡ�èͧ��������͵�ͧ��õԴ��ͼ��Ǻ����к� \r\n\r\n<br/><br/>"
			. "��èͧ��ҹ�����ҧ���� %s %s �֧ %s %s �ͧ %s"
			. " ��� %s ��١ %s.\r\n\r\n<br/><br/>";

// Email that the user gets when the administrator changes their password
$email['password_reset'] = "���� %s ��١�����¼������к� \r\n\r\n"
			. "���ʼ�ҹ���Ǥ��Ǥ�� \r\n\r\n %s\r\n\r\n"
			. "��������ʼ�ҹ���Ǥ��ǹ�� (��ͻ������ͨ����) ����������к� %s ��� %s"
			. " �ҡ����������¹���ʼ�ҹ�����ա����㹡��ͧ�Ǻ�����ǹ��Ǣͧ�����\r\n\r\n"
			. "�ô�Դ��� %s �ҡ�բ��ʧ���";

// Email that the user gets when they change their lost password using the 'Password Reset' form
$email['new_password'] = "%s,\r\n"
            . "Your new password for your %s account is:\r\n\r\n"
            . "%s\r\n\r\n"
            . "Please Log In at %s "
            . "with this new password "
            . "(copy and paste it to ensure it is correct) "
            . "and promptly change your password by clicking the "
            . "Change My Profile Information/Password "
            . "link in My Control Panel.\r\n\r\n"
            . "Please direct any questions to %s.";

// @since 1.1.0
// Email that is sent to invite users to a reservation
$email['reservation_invite'] = "%s  �����س����������ʹ��š����ҹ \r\n\r\n"
		. "ʶҹ��� : %s\r\n"
		. "�ѹ��� : %s\r\n"
		. "���� : %s\r\n"
		. "����ش�ѹ��� : %s\r\n"
		. "����ش���� : %s\r\n"
		. "���������� : %s\r\n"
		. "�ͧ�����ѹ���� (�ҡ��) : %s\r\n\r\n"
		. "�ҡ��ŧ������������꡷���ԧ���� (��ͻ����ԧ���ҡ������ԧ���ҡ�)\n %s\r\n\n"
		. "�ҡ��ŧ������������꡷���ԧ���� (��ͻ����ԧ���ҡ������ԧ���ҡ�)\n %s\r\n\n"
		. "�ҡ�Ѵ�Թ�������ѧ������� %s ��� %s";

// @since 1.1.0
// Email that is sent when a user is removed from a reservation
$email['reservation_removal'] = "�س��١����ʷ㹡����������ҡ���ҧ�ͧ :\r\n\r\n"
		. "ʶҹ��� : %s\r\n"
		. "�ѹ��� : %s\r\n"
		. "���� : %s\r\n"
		. "����ش�ѹ��� : %s\r\n"
		. "����ش���� : %s\r\n"
		. "���������� : %s\r\n"
		. "�ͧ�����ѹ���� (�ҡ��) : %s\r\n\r\n";	
		
// @since 1.2.0
// Email body that is sent for reminders
$email['Reminder Body'] = "���ҧ�ͧ������� %s ����� %s %s ���֧ %s %s ���ж֧��˹��������";
?>