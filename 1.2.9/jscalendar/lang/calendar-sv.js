// ** I18N

// Calendar SV language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("S&ouml;ndag",
 "M&aring;ndag",
 "Tisdag",
 "Onsdag",
 "Torsdag",
 "Fredag",
 "L&ouml;rdag",
 "S&ouml;ndag");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("S&ouml;n",
 "M&aring;n",
 "Tis",
 "Ons",
 "Tor",
 "Fre",
 "L&ouml;r",
 "S&ouml;n");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("Januari",
 "Februari",
 "Mars",
 "April",
 "Maj",
 "Juni",
 "Juli",
 "Augusti",
 "September",
 "Oktober",
 "November",
 "December");

// short month names
Calendar._SMN = new Array
("Jan",
 "Feb",
 "Mar",
 "Apr",
 "Maj",
 "Jun",
 "Jul",
 "Aug",
 "Sep",
 "Okt",
 "Nov",
 "Dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Om kalendern";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"F&ouml;r senaste versionen bes&ouml;k: http://www.dynarch.com/projects/calendar/\n" +
"Distribuerad under GNU LGPL.  Se http://gnu.org/licenses/lgpl.html f&ouml;r detaljer." +
"\n\n" +
"Val av datum:\n" +
"- Anv&auml;nd \xab, \xbb knapparna f&ouml;r att v&auml;lja &aring;r\n" +
"- Anv&auml;nd " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " f&ouml;r att v&auml;lja m&aring;nad\n" +
"- H&aring;ll musknappen nere &ouml;ver knapparna f&ouml;r snabbare val.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Val av tid:\n" +
"- Klicka p&aring; n&aring;gon av delarna av tiden f&ouml;r att &ouml;ka den\n" +
"- eller Shift-klicka f&ouml;r att minska den\n" +
"- eller klicka och drag f&ouml;r ett snabbare val.";

Calendar._TT["PREV_YEAR"] = "F&ouml;reg. &aring;r (h&aring;ll f&ouml;r meny)";
Calendar._TT["PREV_MONTH"] = "F&ouml;reg. m&aring;nad (h&aring;ll f&ouml;r meny)";
Calendar._TT["GO_TODAY"] = "Till Idag";
Calendar._TT["NEXT_MONTH"] = "N&auml;sta m&aring;nad (h&aring;ll f&ouml;r meny)";
Calendar._TT["NEXT_YEAR"] = "N&auml;sta &aring;r (h&aring;ll f&ouml;r meny)";
Calendar._TT["SEL_DATE"] = "V&auml;lj datum";
Calendar._TT["DRAG_TO_MOVE"] = "Drag f&ouml;r att flytta";
Calendar._TT["PART_TODAY"] = " (idag)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Visa %s f&ouml;rst";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "St&auml;ng";
Calendar._TT["TODAY"] = "Idag";
Calendar._TT["TIME_PART"] = "(Shift-)klick eller dra f&ouml;r att byta v&auml;rde";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %e %b";

Calendar._TT["WK"] = "v.";
Calendar._TT["TIME"] = "Tid:";
