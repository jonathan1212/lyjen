<?php
namespace App\Model;
use ZendPdf\PdfDocument;
use ZendPdf\Color;
use ZendPdf\Image;
use ZendPdf\Font;
use Zend\Form\Element;
use Zend\Validator\File\Count;

class RingiDocument extends PdfDocument
{

    protected $language = 'en'; // en => englis, jp=> japan,
    
    protected $single_page;

    protected $approver_count = 5; // max of 5 min of 2
    
    protected $max_approver_count = 5;

    protected $min_approver_count = 2;

    protected $file; // the target path and generated file path 

    protected $approve_location_text = 'Should be approved from';

    protected $branch_name = "ARUZE Gaming America";

    /**
     * Lines and fill color
     */
    protected $line_color = '#585858'; // grey
    
    protected $font_color = '#585858'; // grey
    
    protected $label_background_color = '#FFFFFF'; // label box background color
    
    protected $background_color = '#FFFFFF'; 
    
    protected $text_input_color = '#888888';
    
    protected $draw_lines = true;
    
    
    /**
     * elements paths
     */
    
    protected  $logo_image_path = "";  // Logo path eg. /var/www/site1.com/data/logo/logo.png 
    
    protected  $stamp_location_dir = ""; // the directory of stamps /var/www/site1.com/data/confidentials/approver-stamp/
    
    protected  $pdf_file_dir = ""; // the parent directory of pdf files 
    
    protected  $template_generated = false;
    
    
    /**
     * text inputs
     */
    
    protected $final_approver_comments = ""; // HTML content from db
    
    protected $applicant_name = "";
    
    protected $subject = ""; 
    
    protected $content = " "; // HTML content from db
    
    protected $serial_number = "";
    
    protected $date_issued = "";
   
    protected $requested_deadline_date = "";
    
    protected $status; // approved, denied, suspended
    
    protected $attatchment_count = 0;
    
    protected $approver_list = null;
    
    protected $date_approved;
    
    protected $attachments = array();
    
    protected $approval_authority_number;
    
    protected $suplimentary_advice;
    
    protected $applicant_section;
     
    
    public function __construct ()
    {
        
        parent::__construct();
        $this->single_page = $this->newPage('A4'); // Bond paper portrait
        
    }
    
    
    public function sayHello($_name){
        return "Hello ".$_name;
    }

    /**
     * save the PDF to file
     * 
     * @param string $target_file            
     */
    public function saveToFile ($target_file)
    {
        $path = pathinfo($target_file);
        
        if (! file_exists($path["dirname"]) && ! is_writable($path["dirname"])) {
            throw new \Exception(
                    "Target directory not exist or directory not writable");
        }
        $this->pages[] = $this->single_page;
        
        try{
            $this->save($target_file);
            
        }catch (\ZendPdf\Exception\ExceptionInterface $e){
            // we need to catch this, otherwise error will reveal the secret directory containig the generated pdfs
            exit("PDF not generated. File Directory problem"); 
        }catch (\Exception $e2){
            // we need to catch this, otherwise error will reveal the secret directory containig the generated pdfs
            exit("PDF not generated. File Directory problem"); 
        }
        
        
        $this->setFile($target_file);
    }

    /**
     * export the data to ringi document in PDF format
     * @param file path to target file $target_file
     * @throws \Exception
     * @throws Exception
     * @return string the path to generated pdf file;
     */
    public function exportToPDF($target_file){
        
        //approval list must be set 
        if(count($this->getApporverList()) < 2){
            throw new \Exception(
                    "Ringi Document requires atleast 2 approvers");
        }
        
        $this->setAttachmentCount(count($this->getAttachment()));
        
        if(empty($target_file)){
            throw new \Exception(
                    "Target file directory not set");
        }
        
        $path = pathinfo($target_file);
        
        if (! file_exists($path["dirname"]) && ! is_writable($path["dirname"])) {
            throw new \Exception(
                    "Target directory not exist or directory not writable");
        }
        
        if (count($this->getApporverList()) < $this->getMinimumApproverCount())
            throw new \Exception("Ringi Document requires atleast 2 approvers");
        
        if ($this->getLanguage() == 'en') {
            $this->makeEnTemplate();
        } else if ($this->getLanguage() == 'jp'){
                $this->makeJpTemplate();
            } else {
                throw new \Exception("Language not set");
            }
   
      $this->saveToFile($target_file);   
      return $this->getFile();
    }
    
    public function writeData($data,$x,$y,$font_size,$font_color = '#888888'){
        $this->single_page->setFont(Font::fontWithName(Font::FONT_HELVETICA), $font_size)
        ->setFillColor(Color\Html::color($font_color))
        ->drawText($data, $x, $y,'UTF-8');
    }
    
    public function drawBox($x1,$y1,$x2,$y2,$background_color,$line_color = '#424242'){
        if(!$this->getDrawLines()){
            return;
        }
        // create the approved location
        $this->single_page->setFillColor(new Color\Html($background_color))
        ->setLineColor(new Color\Html($line_color))
        ->drawRectangle($x1, $y1, $x2, $y2);
    }
    
    public function attachImage($image_path,$x1, $y1, $x2, $y2){
        if(!file_exists($image_path))
            throw new \Exception("Image file not accessible ");
        
        $image = Image::imageWithPath($image_path);
        $this->single_page->drawImage($image, $x1, $y1, $x2, $y2);
    }
    
   
    
    public function drawStamp($x,$y,$section,$date,$name){
        $circX = (int)$x;
        $circY = (int)$y;
        $circRad = 24; // fix
        $this->single_page->setFillColor(new Color\Html("#FAFAFA"));
        $this->single_page->setLineColor(new Color\Html("#DF0101"));
        $this->single_page->setLineWidth(2);
        $this->single_page->drawCircle($circX,$circY,$circRad);
        $line1X1 = $circX-24;
        $line1Y1 = $circY+6;
        $this->single_page->drawLine($line1X1, $line1Y1, $line1X1+48, $line1Y1);
        $this->single_page->drawLine($line1X1, $line1Y1-12, $line1X1+48, $line1Y1-12);
        $this->single_page->setLineWidth(1);
        //date
        $this->writeData($date, $circX-17, $circY-3, 7,'#B43104');
        //section
        $section = str_replace('', '', $section);
        $fontSize = 9;
        if(strlen($section)>3){
            $fontSize = 8;
        }
        $this->writeData($this->trimLongText(4,$section), $circX-11, $circY+11, $fontSize,'#B43104');
        //approver name
        $section = str_replace(' ', '', $name);
        $fontSize = 9;
        $nameX = $circX - 15;
        $nameY = $circY - 17;
        if(strlen($name)>6){
            $fontSize = 8;
        }else if(strlen($name)<6 && strlen($name)>=5){
            $nameX = $nameX1+4;
            $fontSize = 10;
        }else if(strlen($name)<=4){
            $nameX = $nameX1+5;
            $fontSize = 10;
        }
        $name = $this->trimLongText(7,$name);
        $this->writeData($name, $nameX, $nameY, $fontSize,'#B43104');
    }
    
    
    
//     public function drawStamp($x,$y,$section,$date,$name){
        
//         $circX = 
//         $this->single_page->setLineColor(new Color\Html("#DF0101"));
//         $this->single_page->setLineWidth(2);
//         $this->single_page->drawCircle(228, 679,26);
//         $this->single_page->drawLine(203, 685, 253, 685);
//         $this->single_page->drawLine(203, 673, 253, 673);
//         $this->single_page->setLineWidth(1);
//         $this->writeData($date, 210, 676, 7,'#B43104');
//         $this->writeData($section, 212, 690, 9,'#B43104');
//         $this->writeData($this->trimLongText(7,$name), 212, 662, 9,'#B43104');
        
//     }
   
    /**
     * Templates En
     * /**
     */
    public function makeEnTemplate (){
  
        $box_line_color = '#585858';
        $label_color = '#484848';
        
        // create the approved location
        $this->drawBox(70, 750, 370, 720, $this->getBackgroundColor(),$label_color);
       
        // write the text
        $this->writeData($this->getApproveLocationText(), 80, 730, 12,$box_line_color);
       
        // draw the logo
        $i_path = DOCUMENT_ROOT."\data\stamp_doc\logo.png";
        $this->attachImage($i_path, 545, 725, 385, 743);
      
        // decision making box label
        $this->drawBox(50, 710, 200, 695, '#FFFFFF',$box_line_color);
        // decision making label 
        $this->writeData("DECISION MAKING", 85, 700, 9,$label_color);
        
        // final approver box on center
        $this->drawBox(200, 710, 390, 650, '#FFFFFF',$box_line_color);
       
        // draw the stamp
        //$this->attachImage(DOCUMENT_ROOT.$this->getFinalApproverStamp(),210, 700, 243, 670);
        $list = $this->getApporverList();
        $this->drawStamp(228, 679, $list[count($list)-1]["section_abbr_name"],\DateTime::createFromFormat("Y-m-d H:i:s", $list[count($list)-1]["last_update"])->format("m.d.Y"), $list[count($list)-1]["user_name"]);
        
        // write the final approver's comment // $data,$x,$y,$char_per_line=125,$height_per_line=9,$color='#888888'
        $this->writeMultilineData($this->getFinalApproverComment(),265,685,29,7,$this->getTextInputColor());
        // date issued label
        $this->drawBox(390, 710, 460, 695, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Date Issued", 400, 700, 9 ,$label_color);
        
        // date issued input
        $this->drawBox(460, 710, 550, 695, $this->getBackgroundColor(),$box_line_color);
        
        // date issued data     
        $this->writeData(\DateTime::createFromFormat("Y-m-d", $this->getDateIssued())->format("m/d/Y"), 465, 700, 8,$this->getTextInputColor());
        
        // requested deadline label
        $this->drawBox(390, 695, 460, 680, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Requested Deadline", 393, 685, 7,$label_color);
        
        
        // requested deadline input
        $this->drawBox(460, 695, 550, 680, $this->getBackgroundColor(),$box_line_color);
        
        //write deadline data
        $this->writeData(\DateTime::createFromFormat("Y-m-d", $this->getRequestedDeadlineDate())->format("m/d/Y"), 465, 685, 8,$this->getTextInputColor());
        
        // serial number label
        $this->drawBox(390, 680, 460, 665, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Serial Number", 395, 670, 9,$label_color);
        
        // serial number input
        $this->drawBox(460, 680, 550, 665, $this->getBackgroundColor(),$box_line_color);

        //write serial number data
        $this->writeData($this->getSerialNumber(),  465, 670, 8,$this->getTextInputColor());
        
        // applicant label
        $this->drawBox(390, 665, 460, 650, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Applicant",405, 655, 9,$label_color);
        
        // applicant input
        $this->drawBox(460, 665, 550, 650, $this->getBackgroundColor(),$box_line_color);
        
        //write applicant name data
        $this->writeData($this->getApplicantName(),465, 655, 8,$this->getTextInputColor());  
        /**
         * back to the left
         */
        // approve box
        $this->drawBox(50, 695, 100, 665, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Approved", 55, 677, 9,$label_color);

        // suspended
        $this->drawBox(100, 695, 150, 665, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Suspended", 105, 677, 8,$label_color);  
        
        // denied
        $this->drawBox(150, 695, 200, 665, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Denied", 160, 677, 9,$label_color);
        
        // date box
        $this->drawBox(50, 665, 100, 650, '#FFFFFF',$box_line_color);
        $this->writeData("Date:", 55, 654, 9,$label_color);
        
        // date input
        $this->drawBox(100, 665, 200, 650, '#FFFFFF',$box_line_color);
        // date data
        $this->writeData(\DateTime::createFromFormat("Y-m-d", $this->getApprovedDate())->format("m/d/Y") ,105, 654, 9,$this->getTextInputColor());
        
        // subject box
        $this->drawBox(50, 650, 100, 620, '#FFFFFF',$box_line_color);
        $this->writeData("Subject:", 55, 630, 9,$label_color);

        // subject input
        $this->drawBox(100, 650, 550, 620, '#FFFFFF',$box_line_color);
  
        //write the subject data
        $this->writeData($this->getSubject(), 105, 630, 8,$this->getTextInputColor());
        
        // main content
        $this->drawBox(50, 620, 550, 250, '#FFFFFF',$box_line_color);
        
        
        $this->writeMultilineData($this->getContent(),65, 585,100,12,$this->getTextInputColor());

        //bottom approval 
        $approver_count = $this->getApproverCount();
        
        //approved/suspende and rejected process 
        $approvers = $this->getApporverList();
        
        if($this->getStatus()=="complete"){
        	$this->attachImage(DOCUMENT_ROOT."\data\stamp_doc\stamps\circle_small.png", 65, 687, 80,670);
        }else if($this->getStatus()=="rejected"){
        	$this->attachImage(DOCUMENT_ROOT."\data\stamp_doc\stamps\circle_small.png", 165, 687, 180,670);
        }else if ($this->getStatus()=="recalled" || $this->getStatus()=="recalled_by_owner"){
        	$this->attachImage(DOCUMENT_ROOT."\data\stamp_doc\stamps\circle_small.png", 115, 687, 130,670);
        }
        
        if($approver_count >= 2){
            $this->make5ApproverTemplateEn();
        }
    
    }
    
    
    /**
     * create 4 approval box at the bottom. 
     */
    private function make5ApproverTemplateEn(){
        $box_line_color = '#585858';
        $label_color = '#484848';
        $approver_list = $this->getApporverList();
        $attachment = $this->getAttachment();
        
        //advance check 
        $this->drawBox(50, 250, 135, 235, '#FFFFFF',$box_line_color);
        //advance check label
        $this->writeData("Advance Check", 60, 240, 9,$label_color);
    
        //1st approver box
        $this->drawBox(135, 250, 220, 235, '#FFFFFF',$box_line_color);
        //1stapprover label
        if (isset($approver_list[0]))
        $this->writeData("1st Approver", 145, 240, 9,$label_color);

        //1st approver stamp box
        $this->drawBox(135, 235, 220, 180, '#FFFFFF',$box_line_color);
        //draw 1st approver stamp
        if (isset($approver_list[0])){
            //$this->attachImage(DOCUMENT_ROOT.$approver_list['approver1']['stamp'], 158, 225, 195, 188);
            $this->drawStamp(176, 207, "RNR",\DateTime::createFromFormat("Y-m-d H:i:s", $approver_list[0]["last_update"])->format("m.d.Y"), $approver_list[0]["user_name"]); // 
        }
        
        
        //2nd approver box
        $this->drawBox(220, 250, 305, 235, '#FFFFFF',$box_line_color);
        //2nd approver label
        if (isset($approver_list[1]) && isset($approver_list[1+1]))
        $this->writeData("2nd Approver", 230, 240, 9,$label_color);
        //2nd approver stamp box
        $this->drawBox(220, 235, 305, 180, '#FFFFFF',$box_line_color);
        //draw 2nd approver stamp
        if (isset($approver_list[1])){
           if(isset($approver_list[1+1]))
            $this->drawStamp(263, 207, $approver_list[1]["section_abbr_name"], \DateTime::createFromFormat("Y-m-d H:i:s", $approver_list[1]["last_update"])->format("m.d.Y"), $approver_list[1]["user_name"]);
        }
        
        
        //3rd approver box
        $this->drawBox(305, 250, 390, 235, '#FFFFFF',$box_line_color);
        //3rd approver label
        if (isset($approver_list[2]) && isset($approver_list[2+1]))
        $this->writeData("3rd Approver", 315, 240, 9,$label_color);
        //3rd approver stamp box
        $this->drawBox(305, 235, 390, 180, '#FFFFFF', $box_line_color);
        // draw 3rd approver stamp
        
        if (isset($approver_list[2])) {
            if(isset($approver_list[2+1]))
            $this->drawStamp(348, 207, $approver_list[2]["section_abbr_name"], \DateTime::createFromFormat("Y-m-d H:i:s", $approver_list[2]["last_update"])->format("m.d.Y"), $approver_list[2]["user_name"]);
        }
        
        //attachement 1 box
        $this->drawBox(390, 250, 550, 235, '#FFFFFF',$box_line_color);
        // attachement 1 label
        $this->writeData("Attachments", 440, 240, 9,$label_color);
        
        //signs box
        $this->drawBox(50, 235, 135, 180, '#FFFFFF',$box_line_color);
        //sign label
        $this->writeData("Signs",75, 205, 9,$label_color);
     
        
        //attachement box
        $this->drawBox(390, 235, 550, 180, '#FFFFFF',$box_line_color);
        
       
        //attachment 1
        if(count($attachment) > 0){
            if(isset($attachment[0]["file_name"]))
            $this->writeData("File # ".$attachment[0]["file_no"]."  (".$attachment[0]["content_type"].")", 410, 205 , 8,$this->getTextInputColor());      
        }
        
        
        //advance check 2 box
        $this->drawBox(50, 180, 135, 165, '#FFFFFF',$box_line_color);
        //advance check 2 label
        $this->writeData("Advance Check", 60, 170, 9,$label_color);
         
        //4th approver label box
        $this->drawBox(135, 180, 220, 165, '#FFFFFF',$box_line_color);
        //4th approver label 
        if (isset($approver_list[3]) && isset($approver_list[3+1]))
        $this->writeData("4th approver ", 145, 170, 9,$label_color);
        
        //blank
        $this->drawBox(220, 180, 305, 165, '#FFFFFF',$box_line_color);
        //blank
        $this->drawBox(305, 180, 390, 165, '#FFFFFF',$box_line_color);
        //attachmement box 2
         $this->drawBox(390, 180, 550, 165, '#FFFFFF',$box_line_color);
         
         $this->writeData("Attachments",440, 170, 9, $box_line_color);
      
         //signs 2 box
        $this->drawBox(50, 165, 135,105, '#FFFFFF',$box_line_color);
        

         
        //signs 2 label
        $this->writeData("Signs", 75, 135, 9,$box_line_color);
        
        //4th approver box
        $this->drawBox(135, 165, 220, 105, '#FFFFFF',$box_line_color);
        
        //blank
        $this->drawBox(220, 165, 305, 105, '#FFFFFF',$box_line_color);
        
        //blank
        $this->drawBox(305,165, 390, 105, '#FFFFFF',$box_line_color);
        
        //box for attachment 
        $this->drawBox(390,165, 550, 105, '#FFFFFF',$box_line_color);
         
        //Suplementary Advice 
        $this->drawBox(50, 105, 135,75, '#FFFFFF',$box_line_color);
        $this->writeData("Suplementary Advice", 55,85, 8, $box_line_color);
       
        $this->drawBox(135, 105, 550,75, '#FFFFFF',$box_line_color);
        $this->writeMultilineData($this->getSuplimentaryAdvice(),145, 92,70,12,$this->getTextInputColor());
        
        //attachment 1
        if(count($attachment) > 0){
            if(isset($attachment[1]["file_name"]))
                $this->writeData("File # ".$attachment[1]["file_no"]."  (".$attachment[1]["content_type"].")", 410, 135 , 8,$this->getTextInputColor());
        }
    }

    
    private function getFinalApproverData(){
        
     $a_list = $this->getApporverList();
     foreach ($a_list as $fa){
         
         if($fa["final_approver"]){
             return $fa;
         }
         
     }
     return null;
    }
    
    public function makeJpTemplate(){
        
        $approver_list = $this->getApporverList();
        
        $box_line_color = '#585858';
        $label_color = '#484848';
  
        //main details box top right of PDF
        $this->drawBox(325, 795, 545, 685, $this->getBackgroundColor(),$box_line_color);
        
        // deadline box label
        $this->drawBox(325, 780, 390, 765, $this->getBackgroundColor(),$box_line_color);
        // deadline label
        $this->writeData("Deadline", 339, 770, 8,$box_line_color);
        // deadline input box
        $this->drawBox(390, 780, 545, 750, $this->getBackgroundColor(),$box_line_color);
        // write the deadline from db
        $image = DOCUMENT_ROOT.implode(SEPARATOR,array('data','pdf','japan','btn_005.png'));
        $this->attachImage($image, 420, 778, 530,768);
        
        $dateStr =  $this->getRequestedDeadlineDate(); //date("Y-m-d")
        $yearStr = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("Y");
        $month = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("m");
        $day = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("d");
        
        $this->writeData($yearStr,400, 770, 9,$this->getTextInputColor());
        $this->writeData($month,453, 770, 9,$this->getTextInputColor());
        $this->writeData($day,500, 770, 9,$this->getTextInputColor());
        
        //issue date label box
        $this->drawBox(325, 795, 390, 780, $this->getBackgroundColor(),$box_line_color);
        // isseu date label
        $this->writeData("Date Issued", 333, 785, 8,$box_line_color);
        // date issue data from db
       
        $graphics005  = implode(SEPARATOR, array('data', 'pdf', 'japan','btn_005.png'));
        $this->attachImage(DOCUMENT_ROOT.$graphics005, 420, 792, 530,783);
        
        $dateStr =  $this->getDateIssued(); //date("Y-m-d")
        $yearStr = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("Y");
        $month = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("m");
        $day = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("d");
        
        $this->writeData($yearStr,400, 785, 9,$this->getTextInputColor());
        $this->writeData($month,453, 785, 9,$this->getTextInputColor());
        $this->writeData($day,500, 785, 9,$this->getTextInputColor());
        
        //serial label box
        $this->drawBox(325, 765, 390, 750, $this->getBackgroundColor(),$box_line_color);
        // serial label
        $this->writeData("Serial No", 340, 755, 8,$box_line_color);
        // serial input box
        $this->drawBox(390, 765, 545, 750, $this->getBackgroundColor(),$box_line_color);
        // serial data
        $this->writeData($this->getSerialNumber(),400 , 755, 9,$this->getTextInputColor());
        
        // section label box
        $this->drawBox(325, 750, 390, 735, $this->getBackgroundColor(),$box_line_color);
        // section label
        $this->writeData("Section", 340, 740, 8,$box_line_color);
        // section inputbox
        $this->drawBox(390, 750, 545, 735, $this->getBackgroundColor(),$box_line_color);
       
        $i_path = implode(SEPARATOR,array('data','pdf','japan')).SEPARATOR.'btn_006.png';
        $this->attachImage($i_path, 392, 748, 480,738);
        
        
        // applicant box label
        $this->drawBox(325, 735, 390, 685, $this->getBackgroundColor(),$box_line_color);
        // applicant label
        $this->writeData("Applicant", 340, 710, 8,$box_line_color);
        // write applicant's name from db
        $this->writeData($this->getApplicantName(),400, 710, 9,$this->getTextInputColor());
       
        //$this->attachImage($i_path, 485, 725, 520,693);
        $this->drawStamp(495,714, "RND", $this->getDateIssued(), $this->getApplicantName());
        
        
        //final approver comment main box
        $this->drawBox(215, 765, 325, 685, $this->getBackgroundColor(),$box_line_color);
        //comment box label
        $this->drawBox(215, 765, 325, 750, $this->getBackgroundColor(),$box_line_color);
        // final approver comment label
        $this->writeData("Comment", 246, 755, 8,$box_line_color);
   
        
        //3 status main box
        $this->drawBox(50, 765, 215, 700, $this->getBackgroundColor(),$box_line_color);
        //approved box label
        $this->drawBox(50, 765, 105, 750, $this->getBackgroundColor(),$box_line_color);
        //approved label
        $this->writeData("Approved", 60, 755, 8,$box_line_color);
        //approved stamp box
        $this->drawBox(50, 750, 105, 685, $this->getBackgroundColor(),$box_line_color);
         //suspended box label
        $this->drawBox(105, 765, 160, 750, $this->getBackgroundColor(),$box_line_color);
        //suspended label
        $this->writeData("Suspended", 110, 755, 8,$box_line_color);
        // suspended stamp box
        $this->drawBox(105, 750, 160, 685, $this->getBackgroundColor(),$box_line_color);
        
        //deny box label
        $this->drawBox(160, 765, 215, 750, $this->getBackgroundColor(),$box_line_color);
        //deny  label
        $this->writeData("Deny", 175, 755, 8,$box_line_color);
        
        if(\strtolower($this->getStatus())=="canceled"){
            $i_path = DOCUMENT_ROOT.implode(SEPARATOR,array('data','stamp_doc','stamps','approver1.png'));
            $this->attachImage($i_path, 170, 740, 205,710);
        }
     
        //date main  box
        $this->drawBox(50, 700, 215, 685, $this->getBackgroundColor(),$box_line_color);
        // date label box
        $this->drawBox(50, 700, 105, 685, $this->getBackgroundColor(),$box_line_color);
        //date label
        $this->writeData("Date",70, 690, 8,$box_line_color);
        //date data  YY  mm  dd
        $i_path = DOCUMENT_ROOT.implode(SEPARATOR,array('data','pdf','japan','btn_005.png'));
        $this->attachImage($i_path, 131, 699,214,688);
        
        
        $dateStr = $this->getApprovedDate(); //date("Y-m-d")
        $yearStr = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("Y");
        $month = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("m");
        $day = \DateTime::createFromFormat("Y-m-d", $dateStr)->format("d");
        
        $this->writeData($yearStr."         ".$month."         ".$day, 115, 690, 8,$this->getTextInputColor());
        
        //subject main  box
        $this->drawBox(50, 685, 545, 660, $this->getBackgroundColor(),$box_line_color);
        //subject box 
        $this->drawBox(50, 685,105, 660, $this->getBackgroundColor(),$box_line_color);
        //subject label
        $this->writeData("Subject: ",65, 670, 8,$label_color);
        //subject content
        $this->writeData($this->getSubject(), 125, 670, 8, $this->getTextInputColor());
        
        //content box
        $this->drawBox(50,660, 545, 370, $this->getBackgroundColor(),$box_line_color);
        $content = $this->getContent()."<p></p> \n".$this->getSuplimentaryAdvice();
        
        $this->writeMultilineData($content,65,630,100,12,$this->getTextInputColor());
        
        
       // draw the japan template middle part
        $i_path = DOCUMENT_ROOT.implode(SEPARATOR,array('data','pdf','japan','pdf_middle.png'));
        $this->attachImage($i_path, 50, 370, 545, 230);
        
        //route row
        $this->drawBox(50, 230, 545, 160, $this->getBackgroundColor(),$box_line_color);
        //route box label
        $this->drawBox(50, 230, 105, 215, $this->getBackgroundColor(),$box_line_color);
        // route label
        $this->writeData("Route", 62, 220, 8,$box_line_color);
        // route sings box
        $this->drawBox(50, 215, 105, 160, $this->getBackgroundColor(),$box_line_color);
        //route "sign" level
        $this->writeData("Signs",68, 185, 8,$box_line_color);
   
        
        
        //approver 1 box label
        $this->drawBox(105, 230, 170, 215, $this->getBackgroundColor(),$box_line_color);
        //approver 1 label
        if (isset($approver_list[1]))
        $this->writeData("Approver 1", 118, 220, 8,$box_line_color);
        //approver 1 stamp box
        $this->drawBox(105, 215, 170, 160, $this->getBackgroundColor(),$box_line_color);
        //approver 2 box label
        $this->drawBox(170, 230, 235, 215, $this->getBackgroundColor(),$box_line_color);
        //approver 2 label
        if (isset($approver_list[2]))
        $this->writeData("Approver 2", 178, 220, 8,$box_line_color);
        //approver 2 stamp box
        $this->drawBox(170, 215, 235, 160, $this->getBackgroundColor(),$box_line_color);
        //approver 3 box label
        $this->drawBox(235, 230, 300, 215, $this->getBackgroundColor(),$box_line_color);
        //approver 3 label
        if (isset($approver_list[3]))
        $this->writeData("Approver 3", 242, 220, 8,$box_line_color);
        //approver 3 stamp box
        $this->drawBox(235, 215, 300, 160, $this->getBackgroundColor(),$box_line_color);
 
        //approver 4 box label
        $this->drawBox(300, 230, 365, 215, $this->getBackgroundColor(),$box_line_color);
        //approver 4 label
        if (isset($approver_list[4]))
        $this->writeData("Approver 4", 308, 220, 8,$box_line_color);
        //approver 4 stamp box
        $this->drawBox(365, 215, 300, 160, $this->getBackgroundColor(),$box_line_color);

        //approver 5 box label
        $this->drawBox(365, 230, 430, 215, $this->getBackgroundColor(),$box_line_color);
        //approver 5 label
        if (isset($approver_list['approver5']))
        $this->writeData("Approver 5", 372, 220, 8,$box_line_color);
        //approver 5 stamp box
        $this->drawBox(365, 215, 430, 160, $this->getBackgroundColor(),$box_line_color);
     
        //hr box label
         $this->drawBox(430, 230, 545, 215, $this->getBackgroundColor(),$box_line_color);
         //hr label
         $this->writeData("HR",470, 220, 8,$box_line_color);
        
        // comments row
        $this->drawBox(50, 160, 545, 90, $this->getBackgroundColor(),$box_line_color);
        //comment label box
        $this->drawBox(50, 160, 105, 90, $this->getBackgroundColor(),$box_line_color);
        $this->writeData("Comments", 60, 123, 8,$box_line_color);
        
        //approver1 comment box input
        $this->drawBox(105, 160, 170, 90, $this->getBackgroundColor(),$box_line_color);
      
        
        //approver2 comment box input
        $this->drawBox(170, 160, 235, 90, $this->getBackgroundColor(),$box_line_color);
     
        //approver3 comment box input
        $this->drawBox(235, 160, 300, 90, $this->getBackgroundColor(),$box_line_color);
      
        
        
        //approver4 comment box input
        $this->drawBox(300, 160, 365, 90, $this->getBackgroundColor(),$box_line_color);
    
        
        //approver5 comment box input
        $this->drawBox(365, 160, 430, 90, $this->getBackgroundColor(),$box_line_color);
        
        //aproval autority and attachment detials row
        $this->drawBox(50, 90, 545, 65, $this->getBackgroundColor(),$box_line_color);
        // label box
        $this->drawBox(50, 90, 105,65, $this->getBackgroundColor(),$box_line_color);
        //label 
        $this->writeData("Approval", 65, 80, 7.5,$box_line_color);
        $this->writeData("Authority No",55, 70, 7.5,$box_line_color);
        // autority input box
        $this->drawBox(105, 90,365,65, $this->getBackgroundColor(),$box_line_color);
        
        //attachement box
        $this->drawBox(300, 90,365,65, $this->getBackgroundColor(),$box_line_color);
        //attachment label
        $this->writeData("Attachments", 310, 75, 8,$box_line_color);
        
        
        $this->writeData("Yes", 405, 75, 8,$box_line_color);
        $this->writeData(".", 430, 75, 12,$box_line_color);
        $this->writeData("No",440, 75, 8,$box_line_color);
        
        //circle yes or no if there's attachment
        $circle_graphics = DOCUMENT_ROOT.implode(SEPARATOR,array('data','stamp_doc','stamps')).SEPARATOR."circle_small.png";
        
        if($this->getAttachmentCount() > 0){
            $this->attachImage($circle_graphics, 403, 70, 418,85);
        }else{
            $this->attachImage($circle_graphics, 437, 70, 452,85);
        }
        
 
        $i_path = DOCUMENT_ROOT.implode(SEPARATOR,array('data','pdf','japan','btn_004.png'));
        $this->attachImage($i_path, 480, 87, 522,73);
        
        if($this->getAttachmentCount()>0){
            $this->writeData($this->getAttachmentCount(),490,75, 12,$box_line_color);
        }
        
        // write final approver's remarks
        //$this->writeData($this->getFinalApproverComment(), 226, 710, 9,$this->getTextInputColor());
        $this->writeMultilineData($this->getFinalApproverComment(), 219, 730,21,10,$this->getTextInputColor());
        
//         $this->writeData("( ",460, 75, 9,$box_line_color);
//         $this->writeData(" )",480, 75, 9,$box_line_color);


        //Draw approver's stamp
        
        $startX = 138;
        $startY = 188;
        $loopCount = 1;
       
        
        foreach ($approver_list as $a){
        	if($loopCount < count($approver_list)){
        		//drow approvers stamp
        		$this->drawStamp($startX, $startY,$a['section_abbr_name'], \DateTime::createFromFormat("Y-m-d H:i:s", $a['last_update'])->format("m.d.Y"), $a['user_name']);
        		// write approvers comments 
        		//write remarks
        		$this->writeMultilineData($a['remarks'], $startX-30, $startY-50, 15, 10,$this->getTextInputColor());
        		 
        		$startX = $startX +64;
        		$loopCount++;
        	}else{
        		//final approver's stamp
        		if($a['status_no'] == 4 || $a['status_no'] == 6){
        			//approved
        			//draw stamp
        			$this->drawStamp(78,725, $a['section_abbr_name'], \DateTime::createFromFormat("Y-m-d H:i:s", $a['last_update'])->format("m.d.Y"), $a['user_name']);
        			
        		}else{
        			$this->drawStamp(188,725, $a['section_abbr_name'], \DateTime::createFromFormat("Y-m-d H:i:s", $a['last_update'])->format("m.d.Y"), $a['user_name']);
        		}
        		
        	}
        
        }
        
        
    }
    
    
    
    
    public function writeMultilineData($data,$x,$y,$char_per_line=125,$height_per_line=9,$color='#888888'){
        
        // Draw text
        $charsPerLine = (int)$char_per_line;
        $heightPerLine = (int)$height_per_line;
        $startingX = (int)$x;
        $font_color = $color;
        
        $text = str_replace('<p>','',$data);
        $lines = array();
        
        foreach (explode("</p>", $text) as $line) {
            $lines = array_merge(
                    $lines,
                    explode(
                            "\n",
                            wordwrap($line, $charsPerLine, "\n")
                    )
            );
        }
        
        $line_count = (int)count($lines);
        
        foreach ( $lines as $i=>$line ) {
           
            $this->writeData($line, $startingX, $y - $i * $heightPerLine, 9, $font_color);
        }
        
    }

    /* Setter and Getters */
    
    /**
     * set the language
     * @default: string 'en'
     * 
     * @param string $lang            
     */
    public function setLanguage ($lang = 'en')
    {
        $this->language = $lang;
    }

    /**
     * get the language
     * 
     * @return string language
     */
    public function getLanguage ()
    {
        return $this->language;
    }

    /**
     *
     * @param number $approver_count            
     */
    public function setApproverCount ($approver_count)
    {
        $this->approver_count = $approver_count;
    }

    /**
     * get number of approver
     * 
     * @return number
     */
    public function getApproverCount ()
    {
        return $this->approver_count;
    }

    /**
     * 
     * @return number - Number of approver
     */
    public function getMinimumApproverCount ()
    {
        return $this->min_approver_count;
    }

    /**
     * @return string  File path to generated PDF file
     */
    public function getFile ()
    {
        return $this->file;
    }

    /**
     * 
     * @param string $file - File path to target PDF file
     */
    public function setFile ($file)
    {
        $this->file = $file;
    }

    /**
     * 
     * @param string $text
     */
    public function setApproveLocationText ($text)
    {
        $this->approve_location_text = $text;
    }

   
    /**
     * @return string 
     */
    public function getApproveLocationText ()
    {
        return $this->approve_location_text;
    }

    /**
     * 
     * @param string  $bname - Branch name
     */
    public function setBranchName ($bname)
    {
        $this->branch_name = $bname;
	}
	
	/**
	 * @return string branch name
	 */
	public function getBranchName(){
		return $this->branch_name;
	}
	
	/**
	 * 
	 * @param string $color html color
	 */
	public function setFontColor($color){
	    $this->font_color = $color;
	}
	
	/**
	 * 
	 * @return string $color html color
	 */
	public function getFontColor(){
	    return $this->font_color;
	}
	
	/**
	 * 
	 * @param string $color
	 */
	public function setLineColor($color){
	    $this->line_color = $color;
	}
	
	/**
	 * 
	 * @return string $color
	 */
	public function getLineColor(){
	    return $this->line_color;
	}
	
	/**
	 * 
	 * @param string $color
	 */
	public function setLabelBackgroundColor($color){
	    $this->label_background_color = $color;
	}
	
	/**
	 * @return string color html color
	 */
	public function getLabelBackgroundColor(){
	    return $this->label_background_color;
	}
	
	/**
	 * 
	 * @param string $color // HTML color
	 */
	public function setBackgroundColor($color){
	    $this->background_color = $color;
	}

	/**
	 * @return boolean
	 */
	public function getBackgroundColor(){
	    return $this->background_color;
	}

	/**
	 * 
	 * @param boolean $t
	 */
	public function setTemplateGenerated($t){
	    $this->template_generated = $t;
	}
	
	/**
	 * @return boolean 
	 */
	public function getTemplateGenerated(){
	    return $this->template_generated;
	}
	
	
	public function setTextInputColor($color){
	    $this->text_input_color = $color;
	}
	
	public function getTextInputColor(){
	    return $this->text_input_color;
	}
	
	public function setContent($content){
	    $this->content = preg_replace("/&#?[a-z0-9]{2,8};/i","",$content);
	}
	
	public function getContent(){
	    return $this->content;
	}
	
	public function setSubject($s){
	    $this->subject = $s;
	}
	
	public function getSubject(){
	   return $this->subject; 
	}
	
	public function setApplicantName($name){
	    $this->applicant_name = $name;
	}
	
	public function  getApplicantName() {
	    return $this->applicant_name;
	}
	
	public function setSerialNumber($sr){
      $this->serial_number = $sr;	    
	}
	
	public function getSerialNumber(){
	    return $this->serial_number;
	}
	
	public function setDateIssued($date){
	   $this->date_issued = $date;    
	}
	
	public function getDateIssued(){
	    return $this->date_issued;
	}
	
	public function setStatus($status){
	   $this->status = $status;
	}
	
	public function getStatus(){
	    return trim($this->status);
	}
	
	public function setAttachmentCount($c){
	    $this->attatchment_count = $c;
	}
	
	public function getAttachmentCount(){
	    return $this->attatchment_count;
	}
	
	public function setRequestedDeadlineDate($dateStr){
	    $this->requested_deadline_date = $dateStr;
	}
	
	public function getRequestedDeadlineDate(){
	    return $this->requested_deadline_date;
	}
	
	public function setApproverList($approver_array) {
	    $this->approver_list = $approver_array;
	}
	
	public function getApporverList(){
	    return $this->approver_list;
	}
	
	public function getApprovedDate(){
	    if(null == $this->date_approved ){
	        return  date("Y-m-d");
	    }
	return $this->date_approved;
	}
	
	public function setApprovedDate($approved_date){
	    $this->date_approved = $approved_date;
	}
	
	public function setAttachment($array_attachments){
	    $this->attachments = $array_attachments;
	}
	
	public function getAttachment(){
	    return $this->attachments;
	}

        public function setFinalApproverComment($text){
              $this->final_approver_comments = $text  ;
        }

          
        public function getFinalApproverComment(){
              return $this->final_approver_comments;
         }
	
	public function setDrawLines($boolean){
	    $this->draw_lines = (boolean)$boolean;
	}
	
    public function getDrawLines(){
        return $this->draw_lines;
    }
    
    //set approval autority number 
    public function setApprovalAuthorityNumber($authority_number){
        $this->approval_authority_number = $authority_number;
    }
    
    //get approval autority number
    public function getApprovalAuthorityNumber(){
       return  $this->approval_authority_number ;
    }
    
    
    public function getSuplimentaryAdvice(){
        return $this->suplimentary_advice;
    }
    
    public function setSuplimentaryAdvice($text){
         $this->suplimentary_advice = $text;
        
    }
    
    public function setApplicantSection($section){
    	$this->applicant_section = $section;
    	
    }
    
    public function getApplicantSection(){
    	return $this->applicant_section;
    }
    
    public function trimLongText($limit=500,$string,$ellipses=true){
        $string = strip_tags($string);
    
        if (strlen($string) > $limit) {
    
            // truncate string
            $string = substr($string, 0, $limit);
    
        }
    
        return $string;
    }
}