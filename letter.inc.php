<?
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	letter
*********************************************************************************/
$romanNum = array('i','ii','iii','iv','v','vi','vii','viii');

class CLetter {
	var $strHeader;
	var $strContent;
	var $strFooter;
	var $nGroup;
	var $strCodeName;
	var $strType;
	var $strSubject;
	var $strToday;
	var $strMonth;
	var $strYear;
	var $strStartYear;
	var $strReceivedDate;
	var $strApprovedDate;
	var $strRejectedDate;
	var $strReceivedDateT;
	var $strApprovedDateT;
	var $strTarikhTamatInsuran;
	var $strNoKenderaan;
	var $strRejectedDateT;
	var $strName;
	var $strMemberID;
	var $strStaffID;
	var $strLoginID;
	var $strNewIC;
	var $strOldIC;
	var $strFundAccount;
	var $strDepartment;
	var $stritem;
	var $strAddress;
	var $strPostcode;
	var $strState;
	var $strInitial;
	var $strTitle;
	var $strHeadLetter;
	var $strGreeting;
	var $strBankAddress;

	var $fMonthFee;
	var $fTotalFee;
	var $fFeeAmount;
	var $fShareAmount;
	var $fDividenAmount;
	var $fDividenBonus;
	var $fDividenPercentage;

	var $fTotalFeeLast;

	var $strSyntaxLibList;
	var $strDataLibList;

	function CLetter() {
	 $this->strHeader  = '';
	 $this->strContent  = '';
	 $this->strFooter  = '';
	 $this->nGroup  	= 0;
	 $this->strCodeName  = '';
	 $this->strType  	= 'SURAT';
	 $this->strToday  	= date('d/m/Y');
	 $this->strDay  	= date('d');
	 $this->strMonth  	= date('m');
	 $this->strYear  	= date('Y');
	 $this->strName  	= '';
	 $this->strMemberID  = '';
	 $this->strStaffID  = '';
	 $this->strLoginID  = '';
	 $this->strNewIC  	= '';
	 $this->strOldIC  	= '';
	 $this->strFundAccount 	= '';
	 $this->strDepartment 	= '';
	 $this->stritem 	= '';
	 $this->strAddress  = '';
	 $this->strPostcode  = '';
	 $this->strState  	= '';
	 $this->strInitial  = '';
	 $this->strTitle  	= '';
	 //$this->strTarikhTamatInsuran='';
	 $this->strGreeting  = 'Salam Sejahtera';

	 // Fee
	 $this->fMonthFee  = 0.0;
	 $this->fTotalFee  = 0.0;

	 $this->fTotalFeeLast  = 0.0;

	 // Dividen
	 $this->fDividenAmount 	= 0.0;
	 $this->fDividenBonus 	= 0.0;
	 $this->fDividenPercentage = 0.0;

	 $this->strSyntaxLibList 	= array();
	 $this->strDataLibList 	= array();
	}

	function SetHeader($strHeader_)	{ $this->strHeader = $strHeader_; }
	function GetHeader()   { return $this->strHeader; }

	function SetContent($strContent_)  { $this->strContent = $strContent_; }
	function GetContent()   { return $this->strContent; }

	function SetFooter($strFooter_)  { $this->strFooter = $strFooter_; }
	function GetFooter()   { return $this->strFooter; }

	function SetGroup($nGroup_)   { $this->nGroup = $nGroup_; }
	function GetGroup()    { return $this->nGroup; }

	function SetCodeName($strCodeName_)  { $this->strCodeName = $strCodeName_; }
	function GetCodeName()   { return $this->strCodeName; }

	function SetType($strType_)   { $this->strType = $strType_; }
	function GetType()    { return $this->strType; }

	function GetToday()    { return $this->strToday; }
	function GetDay()    { return $this->strDay; }
	function GetMonth()    { return $this->strMonth; }
	function GetYear()    { return $this->strYear; }

	function SetStartYear($strValue_)  { $this->strStartYear = $strValue_; }
	function GetStartYear()   { return $this->strStartYear; }

	function SetReceivedDate($strValue_) { $this->strReceivedDate = $strValue_; }
	function GetReceivedDate()   { return $this->strReceivedDate; }

	function SetApprovedDate($strValue_) { $this->strApprovedDate = $strValue_; }
	function GetApprovedDate()   { return $this->strApprovedDate; }

	function SetRejectedDate($strValue_) { $this->strRejectedDate = $strValue_; }
	function GetRejectedDate()   { return $this->strRejectedDate; }

	function SetReceivedDateT($strValue_) { $this->strReceivedDateT = $strValue_; }
	function GetReceivedDateT()   { return $this->strReceivedDateT; }

	function SetApprovedDateT($strValue_) { $this->strApprovedDateT = $strValue_; }
	function GetApprovedDateT()   { return $this->strApprovedDateT; }
//NoKenderaan
	function SetTarikhTamatInsuran($strValue_) { $this->strTarikhTamatInsuran = $strValue_; }
	function GetTarikhTamatInsuran()   { return $this->strTarikhTamatInsuran; }
	
	
	/*
	
	
	function SetName($strValue_)  { $this->strName = $strValue_; }
	function GetName()    { return $this->strName; }
	
	
	
	
	*/
	
	
	
	function SetNoKenderaan($strValue_) { $this->strNoKenderaan = $strValue_; }
	function GetNoKenderaan()   { return $this->strNoKenderaan; }

	function SetRejectedDateT($strValue_) { $this->strRejectedDateT = $strValue_; }
	function GetRejectedDateT()   { return $this->strRejectedDateT; }

	function SetSubject($strValue_)  { $this->strSubject = $strValue_; }
	function GetSubject()   { return $this->strSubject; }

	function SetName($strValue_)  { $this->strName = $strValue_; }
	function GetName()    { return $this->strName; }

	function SetMemberID($strValue_)  { $this->strMemberID = $strValue_; }
	function GetMemberID()   { return $this->strMemberID; }

	function SetStaffID($strValue_)  { $this->strStaffID = $strValue_; }
	function GetStaffID()   { return $this->strStaffID; }

	function SetLoginID($strValue_)  { $this->strLoginID = $strValue_; }
	function GetLoginID()   { return $this->strLoginID; }

	function SetNewIC($strValue_)  { $this->strNewIC = $strValue_; }
	function GetNewIC()    { return $this->strNewIC; }

	function SetOldIC($strValue_)  { $this->strOldIC = $strValue_; }
	function GetOldIC()    { return $this->strOldIC; }

	function SetFundAccount($strValue_)  { $this->FundAccount = $strValue_; }
	function GetFundAccount()   { return $this->FundAccount; }

	function SetDepartment($strValue_)  { $this->strDepartment = $strValue_; }
	function GetDepartment()   { return $this->strDepartment; }

	function Setitem($strValue_)  { $this->stritem = $strValue_; }
	function Getitem()   { return $this->stritem; }

	function SetAddress($strValue_)  { $this->strAddress = $strValue_; }
	function GetAddress()   { return $this->strAddress; }

	function SetPostcode($strValue_)  { $this->strPostcode = $strValue_; }
	function GetPostcode()   { return $this->strPostcode; }

	function SetCity($strValue_)  { $this->strCity = $strValue_; }
	function GetCity()    { return $this->strCity; }

	function SetState($strValue_)  { $this->strState = $strValue_; }
	function GetState()    { return $this->strState; }

	function SetInitial($sex, $maritalID) {
		if ($sex == '0') {
			if ($maritalID == '1') {
				$this->strInitial = 'Tuan';
				$this->strTitle = 'Tn.';
			} else {
				$this->strInitial = 'Encik';
				$this->strTitle = 'En.';
			}
		} else if ($sex == '1') {
			if ($maritalID == '0') {
				$this->strInitial = 'Cik';
				$this->strTitle = 'Cik';
			} else {
				$this->strInitial = 'Puan';
				$this->strTitle = 'Pn.';
			}
		}
	}
	function GetInitial() { return $this->strInitial; }

	function GetTitle() { return $this->strTitle; }

	function SetGreeting($religionID) {
		if ($religionID == '34') {
			$this->strGreeting	= "Assalamualaikum";
		} else {
			$this->strGreeting	= "Salam Sejahtera";
		}
	}
	function GetGreeting() { return $this->strGreeting; }

	function SetHeadLetter($type) {
		

	 if ($type == 0) {
		$this->strHeadLetter = '<br /><br /><br /><br /><br /><br />';
	 } else {
		$int = 1;
	   $coopName = dlookup("setup", "name", "setupID=" . tosql($int, "Text"));
	   $address1 = dlookup("setup", "address1", "setupID=" . tosql($int, "Text"));
	   $address2 = dlookup("setup", "address2", "setupID=" . tosql($int, "Text"));
	   $address3 = dlookup("setup", "address3", "setupID=" . tosql($int, "Text"));
	   $address4 = dlookup("setup", "address4", "setupID=" . tosql($int, "Text"));
	   $noPhone = dlookup("setup", "noPhone", "setupID=" . tosql($int, "Text"));
	   $emel = dlookup("setup", "email", "setupID=" . tosql($int, "Text"));
	   if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));     
	   $Gambar= "upload_images/".$pic;
	   $this->strHeadLetter =
	   '<div align="right" style="font-size:9px;">'.$this->GetCodeName().'</div>'
	   .'<table border="0" cellspacing="0" cellpadding="0" width="100%">'
		.'<tr>'
			.'<td valign="middle" width="100"><img id="elImage" src="'.$Gambar.'" style="height: 110px; width: 110px;" alt="Logo Koperasi"><td>&nbsp;'
			.'<td valign="middle" class="textFont">'
		. $coopName.'<br />'
		. $address1.',<br />'
		. $address2.',<br />'
		. $address3.',<br />'
		. $address4.'.<br />'
		. 'TEL: '.$noPhone.'<br />'
		. 'EMEL: '.$emel.'<br />'
			.'</td>'
		.'</tr>'
	   .'</table>'
	   .'<hr size="1" width="100%">';
	 }
	}
	function GetHeadLetter(){ return $this->strHeadLetter; }

	// Fee
	function SetMonthFee($fValue_){ $this->fMonthFee = $fValue_; }
	function GetMonthFee(){ return $this->fMonthFee; }
	function SetTotalFee($fValue_){ $this->fTotalFee = $fValue_; }
	function GetTotalFee(){ return $this->fTotalFee; }

	function SetTotalFeeLast($fValue_){ $this->fTotalFeeLast = $fValue_; }
	function GetTotalFeeLast(){ return $this->fTotalFeeLast; }
	
	
	
	// Dividen
	function SetDividenAmount($fValue_){ $this->fDividenAmount = $fValue_; }
	function GetDividenAmount(){ return $this->fDividenAmount; }
	
	function SetDividenBonus($fValue_){ $this->fDividenBonus = $fValue_; }
	function GetDividenBonus(){ return $this->fDividenBonus; }
	
	function SetDividenPercentage($fValue_){ $this->fDividenPercentage = $fValue_; }
	function GetDividenPercentage(){ return $this->fDividenPercentage; }

	function SetBankAddress($str){ $this->strBankAddress = $str; }
	function GetBankAddress(){ return $this->strBankAddress; }

	function GetFullAddress() {
	$strAddress_= str_replace('</pre><pre>', '<br />', $this->GetAddress());
	$strAddress_= str_replace('<pre>', '', $strAddress_);
	$strAddress_= str_replace('</pre>', '', $strAddress_);
	$strTemp_ =
	$this->GetTitle().' '.$this->GetName().'<br />'
	.$strAddress_.'<br />'
	.$this->GetPostcode().'&nbsp;'.$this->GetCity().'<br/>'
	.$this->GetState();

	return $strTemp_;
	}

	function GetFullLineupAddress() {
	$tempAddress= strip_tags($this->GetAddress());
	$tempAddress.= ',&nbsp;'.$this->GetPostcode().'&nbsp;'.$this->GetCity();
	$tempAddress.= ',&nbsp;'.$this->GetState();

	return $tempAddress;
	}

	function GetkoopLineupAddress() {
	$tempAddress = 'INSTITUT PENYELIDIKAN PERHUTANAN MALAYSIA (FRIM),52109 KEPONG,SELANGOR DARUL EHSAN';



	return $tempAddress;
	}

	function PrintPage() {
	if ($this->GetType() == 'SURAT') {
		$strTemp_ = $this->GetHeadLetter();
	} else {
		$strTemp_ = '';
	}

	$strTemp_ .= '<div style="page-break-after: always;">';
	if ($this->GetHeader() <> '') {
		$strTemp_ .= '<p>'.$this->CheckSyntax($this->GetHeader()).'</p>';
	}
	if ($this->GetSubject() <> '') {
		$strTemp_ .= '<u><b>'.strtoupper($this->CheckSyntax($this->GetSubject())).'</b></u>';
	}
	if ($this->GetContent() <> '') {
		$strTemp_ .= '<p>'.$this->CheckSyntax($this->GetContent()).'</p>';
	}
	$strTemp_ .= '<p>&nbsp;</p>';
	if ($this->GetFooter() <> '') {
		$strTemp_ .= '<p>'.$this->CheckSyntax($this->GetFooter()).'</p>';
	}
	$strTemp_ .= '</div>';

	return $strTemp_;
	}

	function InitSyntax() {
	// Basic Info
	$this->strSyntaxLibList[] = '[alamat]';
	$this->strDataLibList[] = $this->GetFullAddress();

	$this->strSyntaxLibList[] = '[alamat_perjanjian]';
	$this->strDataLibList[] = $this->GetFullLineupAddress();

	$this->strSyntaxLibList[] = '[alamat_majikan]';
	$this->strDataLibList[] = $this->GetBankAddress();

	$this->strSyntaxLibList[] = '[tuan/puan]';
	$this->strDataLibList[] = $this->GetInitial();

	$this->strSyntaxLibList[] = '[title]';
	$this->strDataLibList[] = $this->GetTitle();

	$this->strSyntaxLibList[] = '[no_anggota]';
	$this->strDataLibList[] = $this->GetMemberID();

	$this->strSyntaxLibList[] = '[nama]';
	$this->strDataLibList[] = $this->GetName();

	$this->strSyntaxLibList[] = '[kp_baru]';
	$this->strDataLibList[] = $this->GetNewIC();

	$this->strSyntaxLibList[] = '[kp_lama]';
	$this->strDataLibList[] = $this->GetOldIC();

	$this->strSyntaxLibList[] = '[jab/caw]';
	$this->strDataLibList[] = $this->GetDepartment();

	//
	$this->strSyntaxLibList[] = '[itemType]';
	$this->strDataLibList[] = $this->Getitem();
	//
	$this->strSyntaxLibList[] = '[tarikh]';
	$this->strDataLibList[] = $this->GetToday();

	$this->strSyntaxLibList[] = '[hari]';
	$this->strDataLibList[] = $this->GetDay();

	$this->strSyntaxLibList[] = '[bulan]';
	$this->strDataLibList[] = displayBulan($this->GetMonth());

	$this->strSyntaxLibList[] = '[tahun]';
	$this->strDataLibList[] = $this->GetYear();

	$this->strSyntaxLibList[] = '[sesi]';
	$this->strDataLibList[] = strval($this->GetYear()-1).'/'.strval($this->GetYear());

	$this->strSyntaxLibList[] = '[tarikh_diterima]';
	$this->strDataLibList[] = $this->GetReceivedDate();

	$this->strSyntaxLibList[] = '[tarikh_lulus]';
	$this->strDataLibList[] = $this->GetApprovedDate();

	$this->strSyntaxLibList[] = '[tarikh_ditolak]';
	$this->strDataLibList[] = $this->GetRejectedDate();

	$this->strSyntaxLibList[] = '[tarikh_diterimaT]';
	$this->strDataLibList[] = $this->GetReceivedDateT();

	$this->strSyntaxLibList[] = '[tarikh_lulusT]';
	$this->strDataLibList[] = $this->GetApprovedDateT();

	$this->strSyntaxLibList[] = '[tarikh_ditolakT]';
	$this->strDataLibList[] = $this->GetRejectedDateT();

	$this->strSyntaxLibList[] = '[no_akaun_tabungan]';
	$this->strDataLibList[] = $this->GetFundAccount();

	$this->strSyntaxLibList[] = '[no_pekerja]';
	$this->strDataLibList[] = $this->GetStaffID();

	// Fee Info
	$this->strSyntaxLibList[] = '[yuran_bulan]';
	$this->strDataLibList[] = number_format($this->GetMonthFee(),2);

	$this->strSyntaxLibList[] = '[jum_yuran_terkumpul]';
	$this->strDataLibList[] = number_format($this->GetTotalFee(),2);

	$this->strSyntaxLibList[] = '[jum_yuran_terkumpul_akhir]';
	$this->strDataLibList[] = number_format($this->GetTotalFeeLast(),2);

	// Dividen info
	$this->strSyntaxLibList[] = '[jum_dividen]';
	$this->strDataLibList[] = number_format($this->GetDividenAmount(),2);

	$this->strSyntaxLibList[] = '[dividen_bonus]';
	$this->strDataLibList[] = number_format($this->GetDividenBonus(),2);

	$this->strSyntaxLibList[] = '[peratus_dividen]';
	$this->strDataLibList[] = number_format($this->GetDividenPercentage(),2);
	//NoKenderaan
	$this->strSyntaxLibList[] = '[TarikhTamatInsuran]';
	$this->strDataLibList[] = $this->GetTarikhTamatInsuran();

	$this->strSyntaxLibList[] = '[NoKenderaan]';
	$this->strDataLibList[] = $this->GetNoKenderaan();

	// Etc
	$this->strSyntaxLibList[] = '><br>';
	$this->strDataLibList[] = '>';
	$this->strSyntaxLibList[] = '><br/>';
	$this->strDataLibList[] = '>';
	$this->strSyntaxLibList[] = '><br />';
	$this->strDataLibList[] = '>';
	}

	//replace code to code value
	function CheckSyntax($strData_) {
	 $strTemp_ = $strData_;
	 $strTemp_ = nl2br($strTemp_);
		
	 //replace current variable in letter to data value data base
	 for ($i = 0; $i < count($this->strSyntaxLibList); $i++) {
		$strTemp_ = str_replace($this->strSyntaxLibList[$i], $this->strDataLibList[$i], $strTemp_);
	 }
		
	 return $strTemp_;
	}

}

class CLetterLoan extends CLetter{
	var $strReceivedDateL;
	var $strApprovedDateL;
	var $strRejectedDateL;

	var $strRefNum;
	var $strLoanID;

	var $strLoanName;
	var $strLoanBondNum;
	
	var $strkomoditi;
	var $strkdruntung;

	var $nLoanPeriod;
	var $nLoanPeriodMonth;
	var $nLoanPeriodMonthLessOne;
	
	var $fLoanAmount;
	var $fLoanProfitRate;
	var $ftotalAmtProfit;
	var $strloanword;
	var $strjualanword;

	var $monthlyAmt;
	var $lastmonthlyAmt;
	var $benefitAmt;
	var $lastbenefitAmt;

	var $monthlyAmtStr;
	var $lastmonthlyAmtStr;
	var $benefitAmtStr;
	var $lastbenefitAmtStr;

	var $totalPayed;
	var $GettotalBalance;
	var $totalBalance;

	var $totalBalanceLast;


	var $bertindih_caj;

	var $strGuarantorName1;
	var $strGuarantorName2;
	var $strGuarantorName3;
	var $strGuarantorID1;
	var $strGuarantorID2;
	var $strGuarantorID3;
	var $strGuarantorNewIC1;
	var $strGuarantorNewIC2;
	var $strGuarantorNewIC3;

	function CLetterLoan(){
	 // Loan
	 CLetter::CLetter();
	 $this->strLoanID 	= '';
	 $this->strRefNum  = '';
	 $this->strLoanName  = '';
	 $this->strkomoditi  = '';
	 $this->strLoanBondNum 	= '';

	 $this->nLoanPeriod  = 0;
	 $this->nLoanPeriodMonth  = 0;
	 $this->nLoanPeriodMonthLessOne  = 0;
	 
	 $this->fLoanAmount  = 0.0;
	 $this->fLoanProfitRate 	= 0.0;
	 //$this->GettotalBalance = 0.0;
	 $this->ftotalAmtProfit = 0.0;
	 $this->strloanword = '';
	 $this->strjualanword = '';
	 
	 $this->monthlyAmt      = 0.0;
	 $this->lastmonthlyAmt  = 0.0;
	 $this->benefitAmt      = 0.0;
	 $this->lastbenefitAmt  = 0.0;

	 $this->monthlyAmtStr      = '';
	 $this->lastmonthlyAmtStr  = '';
	 $this->benefitAmtStr      = '';
	 $this->lastbenefitAmtStr  = '';

	 $this->totalPayed  = 0.0;
	 $this->totalBalance  = 0.0;
	 $this->totalBalanceLast  = 0.0;
	 
	 $this->bertindih_caj = 0.0;

	 $this->strGuarantorName1 = '';
	 $this->strGuarantorName2 = '';
	 $this->strGuarantorName3 = '';
	 $this->strGuarantorID1 	= '';
	 $this->strGuarantorID2 	= '';
	 $this->strGuarantorID3 	= '';
	 $this->strGuarantorNewIC1 = '';
	 $this->strGuarantorNewIC2 = '';
	 $this->strGuarantorNewIC3 = '';
	}

	function SetReceivedDateL($strValue_) { $this->strReceivedDateL = $strValue_; }
	function GetReceivedDateL()   { return $this->strReceivedDateL; }

	function SetApprovedDateL($strValue_) { $this->strApprovedDateL = $strValue_; }
	function GetApprovedDateL()   { return $this->strApprovedDateL; }

	function SetRejectedDateL($strValue_) { $this->strRejectedDateL = $strValue_; }
	function GetRejectedDateL()   { return $this->strRejectedDatLe; }

	// Loan set/get function
	function SetLoanID($strValue_){ $this->strLoanID = $strValue_; }
	function GetLoanID(){ return $this->strLoanID; }

	function SetRefNum($strType_)   { $this->strRefNum = $strType_; }
	function GetRefNum()   { return $this->strRefNum; }
	
	function Setkomoditi($strType_)   { $this->strkomoditi = $strType_; }
	function Getkomoditi()   { return $this->strkomoditi; }
	
	function Setkdruntung($strType_)   { $this->strkdruntung = $strType_; }
	function Getkdruntung()   { return $this->strkdruntung; }

	function SetLoanName($strValue_){ $this->strLoanName = $strValue_; }
	function GetLoanName(){ return $this->strLoanName; }

	function SetLoanBondNum($strValue_){ $this->strLoanBondNum = $strValue_; }
	function GetLoanBondNum(){ return $this->strLoanBondNum; }
	
	//------------------
	function SetLoanPeriod($nValue_){ $this->nLoanPeriod = $nValue_; }
	function GetLoanPeriod(){ return $this->nLoanPeriod; }

	function SetLoanPeriodMonth($nValue_){ $this->nLoanPeriodMonth = $nValue_; }
	function GetLoanPeriodMonth(){ return $this->nLoanPeriodMonth; }

	function SetLoanPeriodMonthLessOne($nValue_){ $this->nLoanPeriodMonthLessOne = $nValue_; }
	function GetLoanPeriodMonthLessOne(){ return $this->nLoanPeriodMonthLessOne; }

	//------------------------
	function SetLoanAmount($fValue_){ $this->fLoanAmount = $fValue_; }
	function GetLoanAmount(){ return $this->fLoanAmount; }

	function SetLoanProfitRate($fValue_){ $this->fLoanProfitRate = $fValue_; }
	function GetLoanProfitRate(){ return $this->fLoanProfitRate; }

	function SetLoanAmtProfit($fValue_){ $this->ftotalAmtProfit = $fValue_; }
	function GetLoanAmtProfit(){ return $this->ftotalAmtProfit; }

	function SetLoanAmountStr($fValue_){ $this->strloanword = $fValue_; }
	function GetLoanAmountStr(){ return $this->strloanword; }

	function SetLoanJualanStr($fValue_){ $this->strjualanword = $fValue_; }
	function GetLoanJualanStr(){ return $this->strjualanword; }
	//-----------------
	
	function SetmonthlyAmt($fValue_){ $this->monthlyAmt = $fValue_; }
	function GetmonthlyAmt(){ return $this->monthlyAmt; }

	function SetlastmonthlyAmt($fValue_){ $this->lastmonthlyAmt = $fValue_; }
	function GetlastmonthlyAmt(){ return $this->lastmonthlyAmt; }
	
	function SetbenefitAmt($fValue_){ $this->benefitAmt = $fValue_; }
	function GetbenefitAmt(){ return $this->benefitAmt; }

	function SetlastbenefitAmt($fValue_){ $this->lastbenefitAmt = $fValue_; }
	function GetlastbenefitAmt(){ return $this->lastbenefitAmt; }	 
	
	//-----------------------
	
	function SetmonthlyStr($str){ $this->monthlyAmtStr = $str; }
	function GetmonthlyStr(){ return $this->monthlyAmtStr; }

	function SetlastmonthlyStr($str){ $this->lastmonthlyAmtStr = $str; }
	function GetlastmonthlyStr(){ return $this->lastmonthlyAmtStr; }
	
	function SetbenefitStr($str){ $this->benefitAmtStr = $str; }
	function GetbenefitStr(){ return $this->benefitAmtStr; }

	function SetlastbenefitStr($str){ $this->lastbenefitAmtStr = $str; }
	function GetlastbenefitStr(){ return $this->lastbenefitAmtStr; }	 
	
	//-----------------------
	function SettotalPayed($fValue_){ $this->totalPayed = $fValue_; }
	function GettotalPayed(){ return $this->totalPayed; }	 

	function SettotalBalance($fValue_){ $this->totalBalance = $fValue_; }
	function GettotalBalance(){ return $this->totalBalance; }	 

	function SettotalBalanceLast($fValue_){ $this->totalBalanceLast = $fValue_; }
	function GettotalBalanceLast(){ return $this->totalBalanceLast; }	 
	
	function Setbayarankhidmat($fValue_){ $this->bertindih_caj = $fValue_; }
	function Getbayarankhidmat(){ return $this->bertindih_caj; }	 

	// Guarantor
	function SetGuarantor1($strName_,$strID_,$strNewIC_) {
		$this->strGuarantorName1 = $strName_;
		$this->strGuarantorID1 = $strID_;
		$this->strGuarantorNewIC1 = $strNewIC_;
	}
	function GetGuarantorName1(){ return $this->strGuarantorName1; }
	function GetGuarantorID1(){ return $this->strGuarantorID1; }
	function GetGuarantorNewIC1(){ return $this->strGuarantorNewIC1; }
	
	function SetGuarantor2($strName_,$strID_,$strNewIC_) {
		$this->strGuarantorName2 = $strName_;
		$this->strGuarantorID2 = $strID_;
		$this->strGuarantorNewIC2 = $strNewIC_;
	}
	function GetGuarantorName2(){ return $this->strGuarantorName2; }
	function GetGuarantorID2(){ return $this->strGuarantorID2; }
	function GetGuarantorNewIC2(){ return $this->strGuarantorNewIC2; }
	
	function SetGuarantor3($strName_,$strID_,$strNewIC_) {
		$this->strGuarantorName3 = $strName_;
		$this->strGuarantorID3 = $strID_;
		$this->strGuarantorNewIC3 = $strNewIC_;
	}
	function GetGuarantorName3(){ return $this->strGuarantorName3; }
	function GetGuarantorID3(){ return $this->strGuarantorID3; }
	function GetGuarantorNewIC3(){ return $this->strGuarantorNewIC3; }

	function InitSyntax(){
	CLetter::InitSyntax();

	//date time loan approval or reject
	$this->strSyntaxLibList[] = '[tarikh_diterimaL]';
	$this->strDataLibList[] = $this->GetReceivedDateL();

	$this->strSyntaxLibList[] = '[tarikh_lulusL]';
	$this->strDataLibList[] = $this->GetApprovedDateL();

	$this->strSyntaxLibList[] = '[tarikh_ditolakL]';
	$this->strDataLibList[] = $this->GetRejectedDateL();

	//loan info
	$this->strSyntaxLibList[] = '[no_rujukan]';
	$this->strDataLibList[] = $this->GetRefNum();
	
	$this->strSyntaxLibList[] = '[no_sijil]';
	$this->strDataLibList[] = $this->Getkomoditi();
	
	$this->strSyntaxLibList[] = '[kadar_u]';
	$this->strDataLibList[] = $this->Getkdruntung();

	$this->strSyntaxLibList[] = '[no_bon_biaya]';
	$this->strDataLibList[] = $this->GetLoanBondNum();

	$this->strSyntaxLibList[] = '[nama_biaya]';
	$this->strDataLibList[] = $this->GetLoanName();

	//--------------------------------	
	$this->strSyntaxLibList[] = '[tempoh_biaya]';
	$this->strDataLibList[] = $this->nLoanPeriod;

	$this->strSyntaxLibList[] = '[tempoh_bulan_biaya]';
	$this->strDataLibList[] = $this->nLoanPeriodMonth; 

	$this->strSyntaxLibList[] = '[tempoh_bulan_biaya-1]';
	$this->strDataLibList[] = $this->nLoanPeriodMonthLessOne;

	$this->strSyntaxLibList[] = '[jum_biaya]';
	$this->strDataLibList[] = number_format($this->fLoanAmount, 2);

	$this->strSyntaxLibList[] = '[kadar_untung]';
	$this->strDataLibList[] = number_format($this->fLoanProfitRate, 2);

	$this->strSyntaxLibList[] = '[jum_biayauntung]';
	$this->strDataLibList[] = number_format($this->ftotalAmtProfit, 2);

	$this->strSyntaxLibList[] = '[jum_biaya_kata]';
	$this->strDataLibList[] = $this->strloanword;

	$this->strSyntaxLibList[] = '[jum_jualan_kata]';
	$this->strDataLibList[] = $this->strjualanword;
	//-----------------------
	$this->strSyntaxLibList[] = '[bayar_bulan]';
	$this->strDataLibList[] = number_format($this->monthlyAmt, 2);

	$this->strSyntaxLibList[] = '[bayar_bulan_akhir]';
	$this->strDataLibList[] = number_format($this->lastmonthlyAmt, 2);

	$this->strSyntaxLibList[] = '[bayar_faedah]';
	$this->strDataLibList[] = number_format($this->benefitAmt, 2);

	$this->strSyntaxLibList[] = '[bayar_faedah_akhir]';
	$this->strDataLibList[] = number_format($this->lastbenefitAmt, 2);
	//----------------------
	$this->strSyntaxLibList[] = '[bayar_bulanStr]';
	$this->strDataLibList[] = $this->monthlyAmtStr;

	$this->strSyntaxLibList[] = '[bayar_bulan_akhirStr]';
	$this->strDataLibList[] = $this->lastmonthlyAmtStr;

	$this->strSyntaxLibList[] = '[bayar_faedahStr]';
	$this->strDataLibList[] = $this->benefitAmtStr;

	$this->strSyntaxLibList[] = '[bayar_faedah_akhirStr]';
	$this->strDataLibList[] = $this->lastbenefitAmtStr;

	$totA = $this->monthlyAmt + $this->benefitAmt;
	$this->strSyntaxLibList[] = '[jumlah_bayarbln]';
	$this->strDataLibList[] = number_format($totA, 2);

	$totB = $this->lastmonthlyAmt + $this->lastbenefitAmt;
	
	$this->strSyntaxLibList[] = '[jumlah_bayarbln_akhir]';
	$this->strDataLibList[] = number_format($totB, 2);
	

	//-----------------------------------------
	$this->strSyntaxLibList[] = '[jum_bayar_balik]';
	$this->strDataLibList[] = number_format($this->totalPayed, 2);

	$this->strSyntaxLibList[] = '[baki_biaya]';
	$this->strDataLibList[] = $this->totalBalance;

	$this->strSyntaxLibList[] = '[baki_biaya_akhir]';
	$this->strDataLibList[] = $this->GettotalBalanceLast();
	
	$this->strSyntaxLibList[] = '[bertindih_caj]';
	$this->strDataLibList[] = number_format($this->bertindih_caj, 2);
	
	// Guarantor Info
	$this->strSyntaxLibList[] = '[nama_penjamin1]';
	$this->strDataLibList[] = $this->GetGuarantorName1();

	$this->strSyntaxLibList[] = '[kp_penjamin1]';
	$this->strDataLibList[] = $this->GetGuarantorNewIC1();

	$this->strSyntaxLibList[] = '[no_anggota_penjamin1]';
	$this->strDataLibList[] = $this->GetGuarantorID1();

	$this->strSyntaxLibList[] = '[nama_penjamin2]';
	$this->strDataLibList[] = $this->GetGuarantorName2();

	$this->strSyntaxLibList[] = '[kp_penjamin2]';
	$this->strDataLibList[] = $this->GetGuarantorNewIC2();

	$this->strSyntaxLibList[] = '[no_anggota_penjamin2]';
	$this->strDataLibList[] = $this->GetGuarantorID2();

	$this->strSyntaxLibList[] = '[nama_penjamin3]';
	$this->strDataLibList[] = $this->GetGuarantorName3();

	$this->strSyntaxLibList[] = '[kp_penjamin3]';
	$this->strDataLibList[] = $this->GetGuarantorNewIC3();

	$this->strSyntaxLibList[] = '[no_anggota_penjamin3]';
	$this->strDataLibList[] = $this->GetGuarantorID3();
	}

}
?>