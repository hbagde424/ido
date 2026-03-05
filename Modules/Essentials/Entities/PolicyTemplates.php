<?php

namespace Modules\Essentials\Entities;

class PolicyTemplates
{
    public static function getTemplate($policy_type)
    {
        $templates = [
            'company_policy' => self::companyPolicyTemplate(),
            'leave_policy' => self::leavePolicyTemplate(),
            'posh_policy' => self::poshPolicyTemplate(),
            'nda_policy' => self::ndaPolicyTemplate(),
        ];

        return $templates[$policy_type] ?? '';
    }
    
    /**
     * Split policy content into pages (approximately 3-4 sections per policy)
     */
    public static function getTemplatePages($policy_type)
    {
        $content = self::getTemplate($policy_type);
        
        // Split by h3 tags to create logical sections
        $sections = preg_split('/<h3>/', $content);
        
        // Group sections into pages (3-4 sections per page)
        $pages = [];
        $currentPage = '';
        $sectionCount = 0;
        
        foreach ($sections as $index => $section) {
            if ($index === 0) {
                $currentPage = $section;
            } else {
                $currentPage .= '<h3>' . $section;
                $sectionCount++;
                
                // Create new page after 3-4 sections
                if ($sectionCount >= 3 && strlen($currentPage) > 1000) {
                    $pages[] = $currentPage;
                    $currentPage = '';
                    $sectionCount = 0;
                }
            }
        }
        
        // Add remaining content
        if (!empty($currentPage)) {
            $pages[] = $currentPage;
        }
        
        return !empty($pages) ? $pages : [$content];
    }

    private static function companyPolicyTemplate()
    {
        return '<div style="margin-bottom: 30px;">
<h2 style="text-align: center; color: #8B1538; margin-bottom: 5px;">HUMAN RESOURCE POLICY MANUAL</h2>
<h3 style="text-align: center; color: #333; margin-top: 0; margin-bottom: 20px;">AKALP TECHNO MEDIA SOLUTIONS</h3>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Introduction - Preface</h3>
<p>In alignment with the vision of transforming Akalp Techno Media Solutions into a leading and respected digital media and technology organization, we firmly believe that our employees are our most valuable asset. To promote transparency, consistency, and efficiency in our operations, it is essential to establish well-defined processes across all functions and levels of the organization.</p>

<p>This Human Resource Policy Manual sets forth the guidelines, rules, and regulations that form the foundation of effective and responsible human resource management. Its purpose is to bring clarity and transparency to our day-to-day working environment, enabling better alignment, improved collaboration, enhanced productivity, and greater job satisfaction.</p>

<p>The HR Manual serves as the first point of reference in an employee\'s journey with Akalp Techno Media Solutions and plays a key role in understanding the organization\'s culture, values, and expectations.</p>

<p><strong>Important Note:</strong> While this Manual provides comprehensive guidance on the Company\'s code of conduct and HR practices, it does not constitute a contract of employment, nor should its contents be interpreted as a contractual obligation. All employees are expected to comply with the policies outlined in this Manual and uphold their intent and spirit at all times. The Management reserves the right to amend, modify, or update any provision of this Manual as and when required to meet organizational or statutory needs.</p>

<h3>Scope & Applicability of the HR Policy</h3>
<p>This Human Resource Policy Manual shall apply to the following categories of personnel engaged by Akalp Techno Media Solutions:</p>
<ul>
    <li><strong>Full-Time Employees</strong> – Individuals employed on a permanent or confirmed basis, working regular hours as defined by the Company (work from office or work from home)</li>
    <li><strong>Part-Time Employees</strong> – Individuals engaged to work for reduced or specified hours as per mutually agreed terms and conditions</li>
    <li><strong>Contractual Employees</strong> – Individuals appointed for a fixed tenure or specific project under a contract agreement with defined start and end dates</li>
    <li><strong>Interns / Trainees</strong> – Individuals engaged for learning, training, or skill development purposes for a specified duration, with or without stipend, as per Company policy</li>
    <li><strong>Consultants / Advisors</strong> – Individuals engaged in a professional or advisory capacity for specialized services, either on a retainer basis or for specific assignments</li>
</ul>

<p>Unless otherwise specifically stated in a particular policy provision, all rules, regulations, code of conduct standards, confidentiality obligations, and disciplinary provisions outlined in this Manual shall apply equally to the above categories.</p>

<p>The applicability of certain benefits, leave entitlements, compensation structures, or statutory provisions may vary depending on the nature of engagement and the terms mentioned in the individual\'s appointment letter, contract agreement, or engagement document. Management reserves the right to determine the extent of policy applicability based on the category of engagement and organizational requirements.</p>

<h3>Objective of the HR Policy Manual</h3>
<p>The primary objective of this HR Policy Manual is to help employees understand and align with Akalp Techno Media Solutions\' Code of Business Conduct. It aims to support employees by:</p>
<ul>
    <li>Strengthening orientation and organizational focus</li>
    <li>Promoting honest, fair, and ethical business practices</li>
    <li>Ensuring continuity and consistency in delivering quality services</li>
    <li>Encouraging open and effective communication</li>
    <li>Driving continuous improvement and innovation</li>
</ul>

<p>The amenities and privileges outlined in this HR Policy Manual represent the current framework and may be revised as the organization grows and evolves. Any updates or modifications to the policies will be communicated to all employees as needed.</p>

<p>All policies stated in this Manual are expected to be followed diligently, and their intent and spirit must be respected under all circumstances.</p>

<h3>HR Mission</h3>
<p>The core mission of the Human Resources Department at Akalp Techno Media Solutions is to support employees, departments, and organizational leadership in achieving both organizational objectives and individual career goals.</p>

<p>We are committed to continuous improvement by hiring individuals with the right skills and mindset, providing ongoing training to enhance capabilities, and nurturing the learning and development of existing employees. Our focus is on ensuring job satisfaction through fair compensation, competitive benefits, and a supportive work environment, while also fostering long-term employee retention.</p>

<p>Akalp Techno Media Solutions strongly believes in upgrading employee competencies through focused development initiatives. We encourage a positive and collaborative workplace culture that promotes teamwork, mutual respect, and shared responsibility. By cultivating determination and self-motivation across all levels, we aim to create a high-performance environment.</p>

<h3>Employment Lifecycle - Recruitment</h3>
<p>The recruitment policy of Akalp Techno Media Solutions is designed to attract and select the best available talent from both internal and external sources, with the objective of strengthening the organization\'s workforce. Recruitment may be undertaken to fill new positions, replace existing staff, facilitate departmental transfers, or due to workforce restructuring.</p>

<p><strong>Key recruitment guidelines:</strong></p>
<ul>
    <li>All internal job vacancies will be communicated to employees through official email communication or by display on the notice board</li>
    <li>Eligible internal candidates are encouraged to apply for open positions</li>
    <li>If suitable internal candidates are not available, the organization will initiate recruitment through external sources</li>
    <li>External recruitment may be carried out through job portals, campus hiring, employee referrals, or other appropriate sourcing channels</li>
    <li>Employees may refer suitable candidates by sharing resumes with the HR Help Desk at the official company email ID</li>
    <li>Internal applicants are required to undergo the complete selection process, similar to external candidates</li>
    <li>The standard selection process includes an HR interview, a technical interview, and/or a skill assessment. The number and type of selection rounds may vary depending on the nature and requirements of the position</li>
    <li>Candidates selected for employment will receive an offer letter via email and must confirm their acceptance by replying to the same email</li>
</ul>

<h3>Employee Referral Program</h3>
<p>This policy outlines the Employee Referral Program at Akalp Techno Media Solutions. We highly value employee referrals, as we believe our employees are well-positioned to identify talent that aligns with our culture, values, and business requirements.</p>

<p>The referral process is designed to be simple and transparent for both employees and the candidates they recommend. For detailed information regarding eligibility, referral guidelines, and applicable criteria, employees are encouraged to connect with the Human Resources Department.</p>

<h3>Training and Development Policy</h3>
<p>The Training and Development framework at Akalp Techno Media Solutions is designed to ensure optimal utilization of the skills and potential of new recruits while providing them with a strong foundation to perform effectively in their roles.</p>

<p><strong>Key training and development guidelines:</strong></p>
<ul>
    <li>All new recruits will undergo a minimum training period of three months, which may be revised based on organizational requirements</li>
    <li>Training for new employees will primarily be conducted through an on-the-job training approach</li>
    <li>In addition to on-the-job training, relevant learning materials or resources may be provided where required</li>
    <li>The immediate reporting manager is responsible for the training and development of the employee</li>
    <li>If a trainee encounters challenges with the training process or methodology, they may approach the Human Resources Department or the Group Project Manager for guidance and support</li>
    <li>Trainees will be assigned to departments based on counseling discussions, individual interests, and their educational background</li>
    <li>Training needs assessments will be conducted at regular intervals to identify skill gaps and development requirements</li>
    <li>Based on the training needs assessment, development programs will be organized for confirmed employees</li>
    <li>Periodic evaluations and assessments will be conducted to measure the effectiveness of the training programs</li>
</ul>

<h3>Probation & Confirmation Policy</h3>
<p><strong>Probation Policy:</strong></p>
<ul>
    <li>All new employees will be on a probation period of six (6) months</li>
    <li>The probation period may be extended at the discretion of Management</li>
    <li>Confirmation of employment will be communicated in writing</li>
    <li>Performance, conduct, discipline, and adaptability will be assessed during probation</li>
    <li>Unsatisfactory performance may result in extension or termination during probation</li>
    <li>The concerned manager will keep a track of the performance of the probationer periodically and will send feedback to the HR Manager</li>
    <li>The tenure of the probation period may vary depending on the feedback of the reporting manager of the employee</li>
</ul>

<h3>Working Arrangement & Time Management</h3>
<p><strong>Working Hours & Shift System:</strong></p>
<ul>
    <li>Office operational hours are from 8:00 AM to 10:00 PM</li>
    <li>The Company operates in two shifts, based on business and operational requirements</li>
    <li>Shift allocation, rotation, and schedules will be determined by Management and communicated in advance</li>
    <li>Employees must strictly adhere to their assigned shift timings</li>
</ul>

<p><strong>While working from home:</strong></p>
<ul>
    <li>All employees should strictly follow the Office Working hours as per provided (with a few exceptions, with prior approval)</li>
    <li>Half hour Lunch break can be availed at any time during the office hours</li>
    <li>Employees are required to log in with their official id. Their attendance, work timings and work log will be monitored</li>
</ul>

<h3>Attendance & Punctuality</h3>
<ul>
    <li>Timely attendance is mandatory</li>
    <li>Late arrivals, early departures, absenteeism, or leaving the workplace without approval may lead to disciplinary action</li>
    <li>Attendance will be monitored using systems prescribed by the Company: Biometric Tracking system, I-do Software, Attendance register & Cameras</li>
    <li>Employees are requested to mark their attendance in the attendance register in front of HR. Marking wrong entries or overwriting in entries will be considered as indiscipline, and action can be taken against them</li>
    <li>Unauthorized absence, poor attendance record, and frequent late coming without any proper explanation will be considered as indiscipline and the organization reserves the right to take any action against the employee on this account</li>
    <li>An employee can be asked by their respective manager, in case of urgency, to work on weekly off/paid holiday</li>
    <li>Employees are expected to follow office timings strictly as any Late Entry will not be entertained, until and unless the employee informs their respective reporting manager, HR, and Employees are asked to be informed by call and if the call is not responded to, then dropping a message will do</li>
    <li><strong>Informed Late Entry (ILE)</strong> - An employee may avail Informed Late Entry (ILE) in case of an emergency, subject to prior intimation to the Reporting Manager and HR. The employee must compensate for the late minutes or hours by extending working time beyond the designated shift on the same day</li>
    <li>A maximum of 1 hour Informed Late Entry (ILE) will be entertained. As a policy a day\'s salary will be deducted for employees making it a habit of late entry and he/she who attends office late for more than five (5) times</li>
    <li><strong>Early Exit (EE)</strong> – Early exit is allowed in case of emergency with formal approval from the respective reporting manager and HR but the number of minutes/hours lost needs to be compensated on the next working day</li>
</ul>

<h3>Leave and Holiday Management</h3>
<p><strong>Leave Policy – Special Conditions:</strong></p>

<p><strong>Leave for Marriage:</strong> Employees applying for leave for their own marriage may avail up to seven (7) days from their annual leave entitlement. Any leave taken beyond seven (7) days will be treated as Leave Without Pay (LWP).</p>

<p><strong>Leave During Probation:</strong> Employees serving their probation period (before confirmation) are not eligible to avail paid leave. Any emergency leave, if approved, shall be considered unpaid and the corresponding salary deduction will apply.</p>

<p><strong>Leave During Notice Period:</strong> Employees are not permitted to avail leave during the notice period. Any unauthorized absence during this time will be treated as a shortfall in the notice period and may be recovered from the employee\'s final settlement.</p>

<p><strong>Leave Audit:</strong> At the end of each calendar year, the leave records of all employees will be reviewed. If an employee has availed leave in excess of the approved annual entitlement, salary deductions will be made for the additional number of days taken.</p>

<p><strong>Leave Without Pay (LWP):</strong> Leave Without Pay may be granted only when an employee has exhausted all available leave entitlements and requires time off due to exceptional circumstances such as medical emergencies or critical personal matters. LWP is subject to written approval and shall be granted solely at the discretion of the CEO, Manager, and HR Department. Any LWP taken without prior written approval will be considered unauthorized leave, and appropriate disciplinary action may be initiated. During the LWP period, employees will not be entitled to any salary or benefits. Weekly offs and holidays falling within the LWP period will also be treated as Leave Without Pay.</p>

<p><strong>Maternity Leave:</strong> Maternity Leave is applicable only to married female employees who have completed a minimum of 180 days of continuous service as a permanent employee at the time of availing the leave. This benefit is applicable for a maximum of two (2) children. The maximum maternity leave entitlement is 60 days, which includes 30 days with full pay (inclusive of 22 earned leaves plus an additional 8 days), and up to 30 additional days without pay, if extended. Maternity Leave may be availed during the final trimester of pregnancy and up to 60 days post-delivery. The leave may also be split into two phases—pre-delivery and post-delivery—provided the total duration does not exceed the prescribed limit. Employees must inform the organization at least three (3) months in advance prior to the intended commencement of maternity leave.</p>

<p><strong>Maternity Leave During Work from Home (WFH):</strong> In view of the operational differences associated with the Work from Home (WFH) model, Akalp Techno Media Solutions shall provide twenty (20) days of maternity leave with full pay to eligible female employees working under the WFH arrangement. This maternity leave under WFH conditions cannot be combined or clubbed with any other category of leave and cannot be extended beyond the approved 20 days. Employees are required to resume work on the 21st day following the completion of the maternity leave period. Failure to report back to work on the stipulated date may result in termination of employment without prior notice, at the discretion of management.</p>

<h3>Leave & Holidays</h3>
<ul>
    <li>Leave entitlement and procedures will be communicated separately by HR</li>
    <li>Diwali holidays: Dhanteras + 5 days (subject to festival option policy)</li>
    <li>Other holidays will be as per the company policy</li>
    <li>Unauthorized leave may result in salary deduction or disciplinary action</li>
</ul>

<h3>Festival Leave & Alternate Work Arrangement Policy</h3>
<p>The Company recognizes the importance of cultural and festival observances and provides flexible leave and work arrangements, subject to business requirements.</p>

<p><strong>1. Diwali Festival Leave Option:</strong> Employees who opt for Diwali festival leave will be entitled to a continuous holiday of Dhanteras + 5 days, as notified by the Company.</p>

<p><strong>2. Alternate Work Arrangement During Diwali Period:</strong> Employees who do not opt for Diwali festival leave may be required to work for up to four (4) days in Work From Home (WFH) mode during the same period, based on role and operational requirements. Employees working from home must remain available during working hours and complete assigned tasks and deliverables.</p>

<p><strong>3. Festival Leave for Bakra Eid and Mithi (Meethi) Eid:</strong> The Company provides two (2) days leave for Eid-ul-Adha (Bakra Eid) and two (2) days leave for Eid-ul-Fitr (Mithi Eid). These leaves are subject to prior approval and business requirements.</p>

<p><strong>4. General Conditions:</strong> Festival leaves are non-transferable and non-cumulative. Festival preferences may be required to be declared in advance. Management reserves the right to approve, modify, or restrict festival leave or WFH arrangements based on operational needs.</p>

<h3>Work From Home (WFH) Policy</h3>
<p>Work From Home (WFH) is not a permanent benefit or employee entitlement. It is a temporary arrangement extended solely at the discretion of Akalp Techno Media Solutions, based on operational feasibility and business requirements.</p>

<p><strong>Eligibility and Approval:</strong></p>
<ul>
    <li>WFH may be availed only after obtaining prior written approval from Management or the Human Resources Department</li>
    <li>Employees are not permitted to assume, request as a matter of right, or self-declare WFH under any circumstances</li>
    <li>Approval of WFH is contingent upon role suitability, past performance, work discipline, and prevailing business needs</li>
</ul>

<p><strong>Work Discipline During WFH:</strong> Employees working under the WFH arrangement are expected to remain available, accessible, and responsive during assigned working hours, log in punctually and adhere strictly to approved shift timings, attend all scheduled meetings, calls, and reviews as directed, and respond promptly to official communications from Management, reporting managers, or team leads. Any form of non-responsiveness, delayed communication, or unavailability during working hours will be treated as misconduct.</p>

<p><strong>Performance and Productivity:</strong> Employees are expected to maintain the same or higher level of productivity as required during office-based work. All assigned responsibilities, deadlines, and deliverables must be completed as scheduled. Unsatisfactory performance during WFH may lead to immediate withdrawal of WFH privileges and may attract disciplinary action.</p>

<p><strong>Data Security and Confidentiality:</strong> Company data must be accessed only through authorized devices, approved software, and secure networks. Downloading, sharing, or storing company data on personal or unauthorized devices is strictly prohibited. Any breach of data security, confidentiality, or misuse of company information during WFH will result in immediate termination and may lead to legal action.</p>

<h3>Compensation & Benefits</h3>
<p>Upon completion of one year of continuous service, employees whose performance meets or exceeds expectations may be eligible for a performance-linked annual increment and/or incentive, subject to Management approval. Special festival bonuses are applicable unless notified separately in writing.</p>

<h3>Performance Management</h3>
<p>The Performance Management System at Akalp Techno Media Solutions is designed to provide a structured and transparent evaluation of employee performance while supporting individual growth and career development. This system also helps identify training and development requirements at regular intervals.</p>

<p>Promotions and recognitions are implemented to encourage continuous improvement, enhance motivation, and ensure leadership continuity across roles.</p>

<p><strong>Key Guidelines:</strong></p>
<ul>
    <li>Every employee is required to submit a weekly campaign/status report to their respective reporting manager, the HR Department, and the CEO. Failure to submit this report may impact performance evaluations and appraisals</li>
    <li>The performance evaluation form shall be completed by the respective reporting manager during the appraisal process</li>
    <li>Inputs and recommendations from the reporting manager, HR Department, and the CEO will be considered to arrive at a comprehensive and final performance assessment</li>
    <li>Employees must complete a self-appraisal as part of the performance review process</li>
    <li>The reporting manager is required to provide structured feedback on the employee\'s self-appraisal, including performance outcomes and any constraints highlighted</li>
    <li>Promotions will be subject to the availability of suitable vacancies at the next level and alignment with role enhancement requirements</li>
    <li>Promotion decisions will be based on evaluation criteria defined by Management at the time of the annual appraisal cycle</li>
    <li>Inter-departmental promotions or role changes may be considered, provided the employee meets the prescribed eligibility and selection criteria</li>
    <li>To be eligible for promotion, an employee must have completed a minimum of six (6) months in their current designation</li>
    <li>Rewards and recognition will be awarded to high-performing employees in acknowledgment of their innovation, initiative, creativity, and overall contribution to the organization</li>
</ul>

<h3>Performance-Linked Salary Structure</h3>
<p>A portion of the employee\'s monthly compensation is linked to performance. 20% of the employee\'s gross monthly salary shall be performance-linked, and 80% shall be fixed compensation.</p>
<p><strong>Example:</strong> Gross Salary ₹20,000 → ₹16,000 fixed + ₹4,000 performance-based.</p>

<p>The performance-linked component is variable in nature and is evaluated based on quality, accuracy, and productivity of work, timely completion of tasks, targets, and deliverables, attendance, punctuality, discipline, and work conduct, compliance with company policies, reporting, and instructions, client feedback, including complaints or negative feedback, and errors, mistakes, negligence, rework, or project lapses attributable to the employee.</p>

<p><strong>Negative Performance Impact Factors:</strong> Repeated late attendance, unapproved early exits, or poor attendance record, failure to submit mandatory reports, non-responsiveness during working hours, failure to meet assigned targets without justified reasons, repeated errors requiring rework, violation of company policies, client dissatisfaction attributable to negligence or misconduct, failure to follow lawful instructions, misrepresentation of work status, damage to company property or data, and any act that adversely affects the Company\'s reputation or client relationships.</p>

<h3>Discipline Management</h3>
<ul>
    <li>All employees shall be expected to observe strict moral and ethical standards in their work and personal life</li>
    <li>All the employees shall follow the company rules and regulations framed from time to time</li>
    <li>All the employees shall follow the job instructions given to them by their superiors and achieve their mutually agreed targets</li>
    <li>Dress in appropriate semi-casual or formal clothing/uniform. Formal attire is mandatory on weekdays. Casual attire is permitted on Saturdays, provided professional standards are maintained</li>
    <li>Keep your mobile phone in silent mode always during working hours</li>
    <li>Keep your workplace clean and organized</li>
    <li>Employees shall avoid loitering, unnecessary idling, or any activity that leads to loss of productive working time</li>
    <li>Employees are entitled to a total break of one (1) hour per working day, which includes two tea/coffee breaks of 15 minutes each and 30 minutes for lunch</li>
    <li>Punctuality is mandatory. Late reporting to work or overstaying during breaks should be strictly avoided</li>
    <li>Smoking is prohibited in restricted areas and during prohibited hours</li>
    <li>The use or chewing of tobacco in any form is strictly prohibited within the office premises</li>
    <li>Employees must not leave the office or workplace without obtaining prior authorization</li>
    <li>Employees shall not make derogatory or defamatory statements about the Company, its management, or its policies</li>
    <li>Employees are expected to follow the organizational hierarchy while accepting instructions and assigning work</li>
    <li>All employees are expected to conduct themselves with honesty, professionalism, and complete personal integrity at all times</li>
    <li>Employees must safeguard company property and ensure strict confidentiality of company information, intellectual property, and client data during and after employment</li>
    <li>No employee shall publish, circulate, or cause to be published any article, post, or content related to the Company in any print, digital, or social media platform without prior written approval from Management</li>
    <li>Employees are not permitted to undertake any part-time or full-time employment outside the organization</li>
    <li>Consumption, possession, or influence of alcohol or any intoxicating or addictive substances within the office premises or workplace is strictly prohibited</li>
    <li>The Company maintains a zero-tolerance policy towards discrimination or harassment on the grounds of race, color, religion, disability, age, gender, marital status, sexual orientation, or citizenship</li>
</ul>

<h3>Prevention of Sexual Harassment (POSH) Policy</h3>
<p>The Company strictly enforces POSH principles and adopts a zero-tolerance approach toward sexual harassment. Sexual harassment includes unwelcome physical contact or advances, requests or demands for sexual favors, sexually colored remarks, jokes, or comments, display or sharing of sexually explicit material, and any unwelcome verbal, non-verbal, or physical conduct of a sexual nature.</p>

<p>This applies to in-person and digital interactions, including calls, email, messaging apps, or virtual platforms. For POSH purposes, "workplace" includes office premises, WFH, client locations, official travel, company events, training programs, and online interactions.</p>

<p>Employees may submit a written complaint to HR or the designated authority at the earliest. All complaints will be handled confidentially, and a fair, impartial inquiry will be conducted. False or malicious complaints will attract disciplinary action. If found guilty, actions may include written warning, salary deduction, suspension, termination, and legal proceedings, if required.</p>

<h3>Confidentiality & Data Protection</h3>
<p>Employees must maintain strict confidentiality of company data, client information, internal processes, and proprietary material during and after employment. Any breach may result in disciplinary action, including termination.</p>

<h3>Termination & Separation Policy</h3>
<ul>
    <li>During the probation period, either party may terminate employment by giving 15 days\' notice</li>
    <li>If an employee leaves during probation without serving the required 15 days\' notice, 15 days\' salary shall be deducted as Loss of Pay (LOP)</li>
    <li>After confirmation, employment may be terminated by either party by giving 30 days\' notice or salary in lieu thereof</li>
    <li>In the unfortunate event of an employee\'s demise, all payable dues shall be released to the employee\'s declared nominee(s)</li>
    <li>Upon separation, employees must complete proper handover and return all company assets and data. Final settlement will be processed only after exit clearance</li>
</ul>

<h3>Amendments</h3>
<p>The Company reserves the right to modify, amend, or update this policy at any time. Employees are required to comply with all revised policies.</p>

<h3>Jurisdiction</h3>
<p>All matters related to employment shall be subject to the Bhopal jurisdiction only.</p>

<h3>Acknowledgment</h3>
<p>By signing this document, I acknowledge that I have read, understood, and agree to comply with all policies outlined in this Human Resource Policy Manual of Akalp Techno Media Solutions.</p>';
    }


    private static function leavePolicyTemplate()
    {
        return '<div style="margin-bottom: 30px;">
<h2 style="text-align: center; color: #8B1538; margin-bottom: 5px;">LEAVE POLICY</h2>
<h3 style="text-align: center; color: #333; margin-top: 0; margin-bottom: 20px;">Akalp Techno Media Solutions LLP</h3>

<p style="line-height: 1.8; margin-bottom: 20px; text-align: justify;">This Leave Policy defines the types of leave, eligibility criteria, approval process, and related conditions applicable to employees of the Company. The Company reserves the right to amend this policy at its discretion.</p>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Applicability</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Applicable to all full-time employees of the Company</li>
    <li>Leave benefits apply to confirmed employees unless otherwise specified</li>
    <li>Probationary employees shall be governed by the probation clause stated herein</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Annual Leave Entitlement (Confirmed Employees)</h3>
<table border="1" cellpadding="15" cellspacing="0" style="width:100%; border-collapse: collapse; margin-bottom: 15px;">
    <tr style="background-color: #8B1538; color: white;">
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Leave Type</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Days Per Year</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Credit Pattern</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Carry Forward</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Remarks</th>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;"><strong>Paid Leave (PL)</strong></td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">6 Days</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Annual</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">-</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Primarily allocated for Diwali Vacation</td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;"><strong>Casual Leave (CL)</strong></td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">6 Days</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">1 Day every 2 months</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">No Carry Forward (Valid till 31st Dec)</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Applicable after probation</td>
    </tr>
</table>
<p style="background-color: #f0f0f0; padding: 12px; border-left: 4px solid #8B1538; margin: 15px 0;"><strong>Total Annual Paid Leave: 12 Days (6 PL + 6 CL)</strong></p>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Casual Leave (CL) - Detailed Guidelines</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Credited at 1 day every 2 months</li>
    <li>Applicable only after successful completion of probation</li>
    <li>Must be utilized within the same calendar year</li>
    <li>Unused CL shall lapse at year-end</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Festival Holidays (Company Declared)</h3>
<table border="1" cellpadding="15" cellspacing="0" style="width:100%; border-collapse: collapse; margin-bottom: 15px;">
    <tr style="background-color: #8B1538; color: white;">
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Festival</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Leave Entitlement</th>
        <th style="text-align: center; padding: 15px; border: 1px solid #8B1538;">Nature</th>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Diwali Vacation</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">6 Days</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Block Leave</td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Holi</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">1 Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Holiday</td>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Raksha Bandhan</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">1 Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Holiday</td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Dussehra</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">1 Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Holiday</td>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Bakri Eid / Eid</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">2 Days</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Conditional / Adjustable</td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Mahashivratri</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">1 Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Conditional</td>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Ram Navami / Ashtami</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Half Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Conditional</td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Christmas</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Half Day</td>
        <td style="text-align: center; padding: 15px; border: 1px solid #ddd;">Holiday</td>
    </tr>
</table>
<p style="background-color: #f0f0f0; padding: 12px; border-left: 4px solid #8B1538; margin: 15px 0;"><strong>Total Festival Holidays: 13 Days</strong></p>
<p style="line-height: 1.8; margin-bottom: 15px;">Festival holidays are subject to annual declaration and business requirements.</p>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Community-Based Holiday Adjustment</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Employees may request substitution of festival holidays based on their religious observance, subject to prior approval</li>
    <li>Holiday adjustments shall be subject to business requirements and Management discretion</li>
    <li>Where operationally required, alternative leave or adjustment may be provided</li>
    <li>Work From Home (WFH) may be permitted where operationally feasible and pre-approved</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">National Holiday</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Gandhi Jayanti (2nd October) – Mandatory Holiday</li>
    <li>Other national holidays shall be observed as per applicable government notifications</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Leave Application & Approval Process</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Leave requests must be submitted via official email or designated HR portal</li>
    <li>Informal communication (WhatsApp, verbal, phone) shall not be treated as valid application</li>
    <li>Prior approval is mandatory before proceeding on leave</li>
    <li>In emergencies, immediate intimation must be followed by written confirmation</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Leave Without Pay (LWP)</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Applicable after exhaustion of PL and CL balances or in case of unapproved leave</li>
    <li>Requires prior written approval except in medical emergencies</li>
    <li>Salary shall not be paid for the period of LWP</li>
    <li>May impact incentives, confirmation, or statutory benefits, where applicable</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Compensatory Off (Comp-Off)</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Granted for working on Sundays or declared holidays with prior approval</li>
    <li>Must be availed within the prescribed time</li>
    <li>Cannot be encashed</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Probationary Employee Leave</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>No paid leave permitted during the first 3 months</li>
    <li>Leave during probation may impact confirmation and salary</li>
    <li>Post confirmation, employee becomes eligible for PL and CL as per policy</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Extended Weekend Leave Guidelines</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Leave applied immediately before or after a weekend (Saturday/Sunday) or declared holiday, resulting in a continuous extended break, shall require prior approval from Management</li>
    <li>In case of sick leave adjoining a weekend or holiday, a valid medical certificate may be required</li>
    <li>If leave is taken without adequate justification or approval in such cases, the intervening weekend/holiday may be treated as Leave Without Pay (LWP)</li>
    <li>Repeated instances of such patterns may be treated as misuse of leave policy and may attract disciplinary action</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Remote Work / Work From Home (WFH)</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Not an entitlement and subject to approval</li>
    <li>Requires prior written approval from Department Head</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Political Campaign & Event Assignment Clause</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">During active political campaigns, election periods, or political event assignments, operational demands may require limited leave flexibility.</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Leave during such periods shall be subject to strict Management approval</li>
    <li>Employees may be required to defer or reschedule planned leave based on project timelines</li>
    <li>Approval shall be granted on a case-by-case basis, considering business exigencies</li>
    <li>In exceptional circumstances, Management reserves the right to recall or cancel previously approved leave, where legally permissible</li>
</ul>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Notice Period Leave Policy</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Leave during the notice period is ordinarily not permitted</li>
    <li>Previously approved leave shall stand cancelled unless specifically re-approved</li>
    <li>Leave during the notice period shall be treated as LWP, except documented medical emergencies</li>
    <li>Unauthorized absence may result in extension of notice period and/or adjustment in full and final settlement, subject to applicable law</li>
</ul>
</div>

<div style="margin-bottom: 30px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">Start-Up Growth & Policy Amendment Clause</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">The Company reserves the right to review, amend, modify, or update this Leave Policy based on business requirements and applicable laws.</p>
<p style="line-height: 1.8;">Employee suggestions and feedback are welcome; interpretation and implementation remain at Management discretion.</p>
</div>

<div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #8B1538;">
<p style="line-height: 1.8; margin-bottom: 20px;"><strong>For and on behalf of</strong><br>
<strong>Akalp Techno Media Solutions LLP</strong></p>

<p style="line-height: 1.8; margin-top: 40px; margin-bottom: 20px;"><strong>Employee Declaration:</strong><br>
I acknowledge and agree to abide by the Company Leave Policy.</p>

<p style="line-height: 2; margin-top: 30px;">Name: _______________________________</p>
<p style="line-height: 2;">Date: _____ / _____ / _______</p>
<p style="line-height: 2;">Signature: _______________________________</p>
</div>';
    }

    private static function poshPolicyTemplate()
    {
        return '<div style="margin-bottom: 30px;">
<h2 style="text-align: center; color: #8B1538; margin-bottom: 5px;">PREVENTION OF SEXUAL HARASSMENT (POSH) POLICY</h2>
<h3 style="text-align: center; color: #333; margin-top: 0; margin-bottom: 20px;">AKALP TECHNO MEDIA SOLUTIONS</h3>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">1. POLICY STATEMENT</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">AKALP Techno Media Solutions is committed to providing a workplace free from sexual harassment. This policy is in compliance with the Sexual Harassment of Women at Workplace (Prevention, Prohibition and Redressal) Act, 2013.</p>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">2. SCOPE</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">This policy applies to all employees, including permanent, temporary, contractual, trainees, and visitors at the workplace.</p>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">3. DEFINITION OF SEXUAL HARASSMENT</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">Sexual harassment includes any unwelcome sexually determined behavior, whether directly or by implication:</p>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.1 Physical Contact and Advances</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Unwelcome physical contact or advances</li>
    <li>Physical confinement against will</li>
    <li>Unwelcome touching, patting, pinching</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.2 Verbal Harassment</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Sexually colored remarks</li>
    <li>Jokes or comments of sexual nature</li>
    <li>Requests or demands for sexual favors</li>
    <li>Sexually degrading words</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.3 Non-Verbal Harassment</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Showing pornography or sexual images</li>
    <li>Leering or staring</li>
    <li>Sexually suggestive gestures</li>
    <li>Display of sexually offensive material</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.4 Digital Harassment</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Sending sexually explicit emails or messages</li>
    <li>Sharing inappropriate images or videos</li>
    <li>Making sexual comments on social media</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">4. INTERNAL COMPLAINTS COMMITTEE (ICC)</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">4.1 Constitution</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Presiding Officer: Senior woman employee</li>
    <li>Two members from amongst employees</li>
    <li>One external member from NGO or familiar with sexual harassment issues</li>
    <li>At least 50% members shall be women</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">4.2 Tenure</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Members serve for a period of 3 years</li>
    <li>No member can serve for more than 2 consecutive terms</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">5. COMPLAINT PROCEDURE</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">5.1 Filing a Complaint</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Complaint to be filed within 3 months of incident</li>
    <li>Extended period of 3 months if circumstances prevented filing</li>
    <li>Written complaint to ICC with details of incident</li>
    <li>Complaint can be filed in person or via email</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">5.2 Complaint Details</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Name and address of complainant</li>
    <li>Name and address of respondent</li>
    <li>Details of incident(s) with dates and times</li>
    <li>Names of witnesses, if any</li>
    <li>Supporting documents, if available</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">6. INQUIRY PROCESS</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">6.1 Initial Assessment</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>ICC to acknowledge complaint within 7 days</li>
    <li>Preliminary inquiry to determine if case falls under POSH</li>
    <li>Conciliation may be attempted if complainant requests</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">6.2 Investigation</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Inquiry to be completed within 90 days</li>
    <li>Both parties given opportunity to present their case</li>
    <li>Witnesses examined if required</li>
    <li>Confidentiality maintained throughout</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">6.3 Interim Relief</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Transfer of complainant or respondent</li>
    <li>Grant of leave to complainant</li>
    <li>Restraining order against respondent</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">7. INQUIRY REPORT</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Report submitted within 10 days of completion</li>
    <li>Recommendations for action if harassment proved</li>
    <li>Copy provided to both parties</li>
    <li>Action to be taken within 60 days</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">8. PENALTIES</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">If sexual harassment is proved, the following actions may be taken:</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Written apology</li>
    <li>Warning or reprimand</li>
    <li>Withholding of promotion or increment</li>
    <li>Suspension without pay</li>
    <li>Termination of employment</li>
    <li>Deduction from salary for compensation to complainant</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">9. FALSE COMPLAINTS</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Malicious complaints will be treated seriously</li>
    <li>Action taken against complainant if complaint found false</li>
    <li>Genuine complaints made in good faith will not be penalized even if not proved</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">10. CONFIDENTIALITY</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>All proceedings kept confidential</li>
    <li>Identity of complainant, respondent, and witnesses protected</li>
    <li>Breach of confidentiality subject to disciplinary action</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">11. PROTECTION AGAINST RETALIATION</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>No retaliation against complainant or witnesses</li>
    <li>Any retaliation will be treated as misconduct</li>
    <li>Complainant can report retaliation to ICC</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">12. AWARENESS AND TRAINING</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Regular awareness programs on POSH</li>
    <li>Training for ICC members</li>
    <li>Policy displayed at prominent places</li>
    <li>Annual report on POSH submitted to authorities</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">13. CONTACT DETAILS</h3>
<p style="line-height: 1.8; margin-bottom: 10px;"><strong>Internal Complaints Committee:</strong></p>
<p style="line-height: 1.8; margin-bottom: 5px;">Email: icc@akalptechnomediasolutions.com</p>
<p style="line-height: 1.8; margin-bottom: 15px;">Phone: +91 8085504485</p>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">14. ACKNOWLEDGMENT</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">I acknowledge that I have read and understood the POSH Policy and commit to maintaining a harassment-free workplace.</p>';
    }

    private static function ndaPolicyTemplate()
    {
        return '<div style="margin-bottom: 30px;">
<h2 style="text-align: center; color: #8B1538; margin-bottom: 5px;">NON-DISCLOSURE AGREEMENT (NDA) POLICY</h2>
<h3 style="text-align: center; color: #333; margin-top: 0; margin-bottom: 20px;">AKALP TECHNO MEDIA SOLUTIONS</h3>
</div>

<div style="margin-bottom: 25px;">
<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">1. PURPOSE</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">This Non-Disclosure Agreement (NDA) is designed to protect confidential and proprietary information of AKALP Techno Media Solutions and its clients.</p>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">2. PARTIES</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">This agreement is between:</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li><strong>Disclosing Party:</strong> AKALP Techno Media Solutions</li>
    <li><strong>Receiving Party:</strong> Employee (as named in this document)</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px;">3. DEFINITION OF CONFIDENTIAL INFORMATION</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">Confidential Information includes, but is not limited to:</p>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.1 Business Information</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Business plans and strategies</li>
    <li>Financial information and projections</li>
    <li>Marketing and sales strategies</li>
    <li>Customer lists and databases</li>
    <li>Supplier and vendor information</li>
    <li>Pricing information and cost structures</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.2 Technical Information</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Source code and software</li>
    <li>Algorithms and processes</li>
    <li>Technical specifications and designs</li>
    <li>Research and development projects</li>
    <li>Patents, trademarks, and copyrights</li>
    <li>Trade secrets and know-how</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.3 Client Information</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Client names and contact details</li>
    <li>Project details and requirements</li>
    <li>Client business information</li>
    <li>Contracts and agreements</li>
    <li>Client feedback and communications</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">3.4 Employee Information</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Employee personal data</li>
    <li>Salary and compensation details</li>
    <li>Performance evaluations</li>
    <li>Internal communications</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">4. OBLIGATIONS OF RECEIVING PARTY</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">4.1 Non-Disclosure</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Not disclose confidential information to any third party</li>
    <li>Not use confidential information for personal benefit</li>
    <li>Protect confidential information with reasonable care</li>
    <li>Limit access to confidential information on need-to-know basis</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">4.2 Use of Information</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Use confidential information only for authorized business purposes</li>
    <li>Not reproduce or copy confidential information without permission</li>
    <li>Not reverse engineer or attempt to derive source code</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">4.3 Security Measures</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Use strong passwords and authentication</li>
    <li>Encrypt sensitive data</li>
    <li>Lock computers and devices when unattended</li>
    <li>Report any security breaches immediately</li>
    <li>Follow company IT security policies</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">5. EXCEPTIONS</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">Confidential Information does not include information that:</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Is publicly available through no fault of receiving party</li>
    <li>Was known to receiving party before disclosure</li>
    <li>Is independently developed by receiving party</li>
    <li>Is received from a third party without breach of obligation</li>
    <li>Is required to be disclosed by law or court order</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">6. RETURN OF INFORMATION</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">Upon termination of employment or upon request:</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Return all confidential information and materials</li>
    <li>Delete all electronic copies of confidential information</li>
    <li>Certify in writing that all information has been returned/destroyed</li>
    <li>Return company property including laptops, phones, documents</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">7. DURATION OF OBLIGATION</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Obligations continue during employment</li>
    <li>Obligations continue for 3 years after termination of employment</li>
    <li>Trade secrets protected indefinitely</li>
    <li>Obligations survive termination of employment</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">8. INTELLECTUAL PROPERTY</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">8.1 Work Product</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>All work created during employment belongs to company</li>
    <li>Employee assigns all rights to company</li>
    <li>Includes inventions, designs, code, documents</li>
    <li>Employee waives moral rights to work product</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">8.2 Prior Inventions</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Employee to disclose any prior inventions</li>
    <li>Prior inventions excluded from company ownership</li>
    <li>List of prior inventions attached to agreement</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">9. NON-COMPETE CLAUSE</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Employee shall not work for direct competitors during employment</li>
    <li>Non-compete period: 1 year after termination</li>
    <li>Geographic restriction: India</li>
    <li>Applies to similar roles and responsibilities</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">10. NON-SOLICITATION</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Not solicit company employees for 2 years after termination</li>
    <li>Not solicit company clients for 2 years after termination</li>
    <li>Not interfere with company business relationships</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">11. BREACH AND REMEDIES</h3>
<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">11.1 Consequences of Breach</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Immediate termination of employment</li>
    <li>Legal action for damages</li>
    <li>Injunctive relief to prevent further disclosure</li>
    <li>Recovery of legal costs and expenses</li>
</ul>

<h4 style="color: #555; margin-top: 15px; margin-bottom: 10px;">11.2 Reporting Breaches</h4>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Report any suspected breach immediately</li>
    <li>Cooperate in investigation of breaches</li>
    <li>Take corrective action as directed</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">12. PERMITTED DISCLOSURES</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Disclosure to authorized company personnel</li>
    <li>Disclosure required by law (with prior notice to company)</li>
    <li>Disclosure with written consent of company</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">13. THIRD PARTY INFORMATION</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Respect confidentiality of third party information</li>
    <li>Not disclose third party information without authorization</li>
    <li>Follow third party confidentiality agreements</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">14. GOVERNING LAW</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>Agreement governed by laws of India</li>
    <li>Jurisdiction: Courts of Bhopal, Madhya Pradesh</li>
    <li>Disputes resolved through arbitration if possible</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">15. ENTIRE AGREEMENT</h3>
<ul style="line-height: 2; margin-left: 20px;">
    <li>This agreement constitutes entire understanding</li>
    <li>Supersedes all prior agreements</li>
    <li>Amendments must be in writing</li>
    <li>Severability clause applies</li>
</ul>

<h3 style="color: #8B1538; border-bottom: 2px solid #8B1538; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px;">16. ACKNOWLEDGMENT AND ACCEPTANCE</h3>
<p style="line-height: 1.8; margin-bottom: 15px;">I acknowledge that:</p>
<ul style="line-height: 2; margin-left: 20px;">
    <li>I have read and understood this NDA Policy</li>
    <li>I agree to be bound by all terms and conditions</li>
    <li>I understand the consequences of breach</li>
    <li>I will protect confidential information</li>
    <li>I will comply with all obligations during and after employment</li>
</ul>

<p style="line-height: 1.8; margin-bottom: 15px;"><strong>By signing below, I accept and agree to all terms of this Non-Disclosure Agreement.</strong></p>';
    }
}
