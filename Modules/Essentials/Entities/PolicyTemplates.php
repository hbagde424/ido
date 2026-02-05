<?php

namespace Modules\Essentials\Entities;

class PolicyTemplates
{
    public static function getTemplate($policy_type)
    {
        $templates = [
            'company_policy' => self::companyPolicyTemplate(),
            'hr_policy' => self::hrPolicyTemplate(),
            'leave_policy' => self::leavePolicyTemplate(),
            'posh_policy' => self::poshPolicyTemplate(),
            'nda_policy' => self::ndaPolicyTemplate(),
        ];

        return $templates[$policy_type] ?? '';
    }

    private static function companyPolicyTemplate()
    {
        return '<h2>COMPANY POLICY</h2>

<h3>1. PURPOSE</h3>
<p>This Company Policy outlines the standards of conduct and expectations for all employees of AKALP Techno Media Solutions. The purpose is to ensure a professional, productive, and harmonious work environment.</p>

<h3>2. SCOPE</h3>
<p>This policy applies to all employees, contractors, consultants, and temporary workers at AKALP Techno Media Solutions.</p>

<h3>3. CODE OF CONDUCT</h3>
<h4>3.1 Professional Behavior</h4>
<ul>
    <li>Employees must maintain professional conduct at all times</li>
    <li>Respect colleagues, clients, and company property</li>
    <li>Adhere to company dress code and appearance standards</li>
    <li>Maintain punctuality and regular attendance</li>
</ul>

<h4>3.2 Workplace Ethics</h4>
<ul>
    <li>Act with integrity and honesty in all business dealings</li>
    <li>Avoid conflicts of interest</li>
    <li>Report any unethical behavior or violations</li>
    <li>Maintain confidentiality of company information</li>
</ul>

<h3>4. WORK HOURS AND ATTENDANCE</h3>
<ul>
    <li>Standard working hours: 9:00 AM to 6:00 PM (Monday to Saturday)</li>
    <li>Employees must inform their supervisor of any absence</li>
    <li>Unauthorized absence may result in disciplinary action</li>
    <li>Overtime work requires prior approval</li>
</ul>

<h3>5. COMMUNICATION POLICY</h3>
<ul>
    <li>Use official communication channels for work-related matters</li>
    <li>Maintain professional language in all communications</li>
    <li>Respond to emails and messages within 24 hours</li>
    <li>Respect confidentiality in all communications</li>
</ul>

<h3>6. TECHNOLOGY AND EQUIPMENT USE</h3>
<ul>
    <li>Company equipment is for business use only</li>
    <li>Employees are responsible for company property assigned to them</li>
    <li>Report any damage or loss immediately</li>
    <li>Follow IT security policies and guidelines</li>
</ul>

<h3>7. HEALTH AND SAFETY</h3>
<ul>
    <li>Maintain a safe and clean work environment</li>
    <li>Report any safety hazards immediately</li>
    <li>Follow emergency procedures and protocols</li>
    <li>Participate in safety training programs</li>
</ul>

<h3>8. DISCIPLINARY ACTIONS</h3>
<p>Violation of company policies may result in:</p>
<ul>
    <li>Verbal warning</li>
    <li>Written warning</li>
    <li>Suspension</li>
    <li>Termination of employment</li>
</ul>

<h3>9. POLICY UPDATES</h3>
<p>This policy may be updated from time to time. Employees will be notified of any changes and are expected to comply with the updated policy.</p>

<h3>10. ACKNOWLEDGMENT</h3>
<p>By signing this document, I acknowledge that I have read, understood, and agree to comply with the Company Policy of AKALP Techno Media Solutions.</p>';
    }

    private static function hrPolicyTemplate()
    {
        return '<h2>HUMAN RESOURCES POLICY</h2>

<h3>1. RECRUITMENT AND SELECTION</h3>
<h4>1.1 Hiring Process</h4>
<ul>
    <li>All positions will be advertised internally and externally</li>
    <li>Selection based on merit, qualifications, and experience</li>
    <li>Background verification mandatory for all new hires</li>
    <li>Equal opportunity employer - no discrimination</li>
</ul>

<h4>1.2 Probation Period</h4>
<ul>
    <li>New employees serve a probation period of 3-6 months</li>
    <li>Performance evaluation at the end of probation</li>
    <li>Confirmation subject to satisfactory performance</li>
</ul>

<h3>2. COMPENSATION AND BENEFITS</h3>
<h4>2.1 Salary Structure</h4>
<ul>
    <li>Competitive salary based on industry standards</li>
    <li>Annual performance-based increments</li>
    <li>Salary paid on the last working day of each month</li>
    <li>Salary slips provided via email</li>
</ul>

<h4>2.2 Benefits</h4>
<ul>
    <li>Provident Fund (PF) contribution</li>
    <li>Employee State Insurance (ESI) coverage</li>
    <li>Health insurance for employees and dependents</li>
    <li>Annual bonus based on performance</li>
</ul>

<h3>3. PERFORMANCE MANAGEMENT</h3>
<h4>3.1 Performance Appraisal</h4>
<ul>
    <li>Annual performance review cycle</li>
    <li>Goal setting and KPI tracking</li>
    <li>360-degree feedback mechanism</li>
    <li>Performance improvement plans when needed</li>
</ul>

<h4>3.2 Training and Development</h4>
<ul>
    <li>Regular training programs and workshops</li>
    <li>Skill development opportunities</li>
    <li>Career advancement pathways</li>
    <li>Mentorship programs</li>
</ul>

<h3>4. EMPLOYEE RELATIONS</h3>
<h4>4.1 Grievance Handling</h4>
<ul>
    <li>Open-door policy for employee concerns</li>
    <li>Confidential grievance redressal mechanism</li>
    <li>Resolution within 15 working days</li>
    <li>Appeal process available</li>
</ul>

<h4>4.2 Employee Engagement</h4>
<ul>
    <li>Regular team building activities</li>
    <li>Employee recognition programs</li>
    <li>Celebration of festivals and achievements</li>
    <li>Employee satisfaction surveys</li>
</ul>

<h3>5. SEPARATION POLICY</h3>
<h4>5.1 Resignation</h4>
<ul>
    <li>Notice period: 30-60 days based on position</li>
    <li>Exit interview mandatory</li>
    <li>Full and final settlement within 45 days</li>
    <li>Return of company property required</li>
</ul>

<h4>5.2 Termination</h4>
<ul>
    <li>Termination with cause: immediate</li>
    <li>Termination without cause: notice period applicable</li>
    <li>Severance pay as per company policy</li>
</ul>

<h3>6. EMPLOYEE RECORDS</h3>
<ul>
    <li>Confidential employee records maintained</li>
    <li>Employees can access their records</li>
    <li>Updates to personal information must be reported</li>
    <li>Data protection and privacy maintained</li>
</ul>

<h3>7. COMPLIANCE</h3>
<ul>
    <li>Adherence to labor laws and regulations</li>
    <li>Regular policy reviews and updates</li>
    <li>Employee awareness programs</li>
</ul>

<h3>8. ACKNOWLEDGMENT</h3>
<p>I acknowledge that I have read and understood the HR Policy and agree to abide by all terms and conditions.</p>';
    }

    private static function leavePolicyTemplate()
    {
        return '<h2>LEAVE POLICY</h2>

<h3>1. PURPOSE</h3>
<p>This Leave Policy defines the types of leave available to employees and the procedures for requesting and approving leave.</p>

<h3>2. TYPES OF LEAVE</h3>

<h4>2.1 Casual Leave (CL)</h4>
<ul>
    <li>Entitlement: 12 days per year</li>
    <li>Can be taken for personal reasons</li>
    <li>Minimum 1 day notice required</li>
    <li>Cannot be carried forward</li>
    <li>Cannot be encashed</li>
</ul>

<h4>2.2 Sick Leave (SL)</h4>
<ul>
    <li>Entitlement: 12 days per year</li>
    <li>Medical certificate required for 3+ consecutive days</li>
    <li>Can be taken without prior notice in emergencies</li>
    <li>Unused sick leave can be accumulated up to 90 days</li>
</ul>

<h4>2.3 Earned Leave (EL) / Privilege Leave</h4>
<ul>
    <li>Entitlement: 15 days per year</li>
    <li>Accrued monthly (1.25 days per month)</li>
    <li>Minimum 7 days notice required</li>
    <li>Can be carried forward to next year</li>
    <li>Encashable at the time of resignation</li>
</ul>

<h4>2.4 Maternity Leave</h4>
<ul>
    <li>Entitlement: 26 weeks (6 months) for first two children</li>
    <li>12 weeks for third child onwards</li>
    <li>Medical certificate required</li>
    <li>Full salary during maternity leave</li>
    <li>Can be availed 8 weeks before expected delivery date</li>
</ul>

<h4>2.5 Paternity Leave</h4>
<ul>
    <li>Entitlement: 15 days</li>
    <li>Can be availed within 6 months of child birth</li>
    <li>Birth certificate required</li>
    <li>Full salary during paternity leave</li>
</ul>

<h4>2.6 Bereavement Leave</h4>
<ul>
    <li>Entitlement: 5 days per occurrence</li>
    <li>For death of immediate family member</li>
    <li>Death certificate required</li>
    <li>Additional leave can be taken as casual leave</li>
</ul>

<h4>2.7 Compensatory Off (Comp Off)</h4>
<ul>
    <li>Granted for working on weekly off or holidays</li>
    <li>Must be availed within 30 days</li>
    <li>Prior approval from manager required</li>
    <li>Cannot be carried forward or encashed</li>
</ul>

<h3>3. LEAVE APPLICATION PROCESS</h3>
<h4>3.1 Planned Leave</h4>
<ul>
    <li>Apply through HRMS portal</li>
    <li>Submit application at least 7 days in advance</li>
    <li>Manager approval required</li>
    <li>HR approval for leaves exceeding 5 days</li>
</ul>

<h4>3.2 Emergency Leave</h4>
<ul>
    <li>Inform manager immediately via phone/email</li>
    <li>Submit formal application within 24 hours</li>
    <li>Supporting documents if required</li>
</ul>

<h3>4. LEAVE APPROVAL</h3>
<ul>
    <li>Manager has authority to approve/reject leave</li>
    <li>Approval based on work requirements and team availability</li>
    <li>Employees notified of approval/rejection within 48 hours</li>
    <li>Rejected leave can be reapplied with different dates</li>
</ul>

<h3>5. LEAVE BALANCE</h3>
<ul>
    <li>Leave balance visible in HRMS portal</li>
    <li>Monthly leave balance statement sent via email</li>
    <li>Negative leave balance not permitted</li>
    <li>Leave without pay (LWP) if leave balance exhausted</li>
</ul>

<h3>6. HOLIDAYS</h3>
<ul>
    <li>Annual holiday calendar published at year beginning</li>
    <li>National and regional holidays observed</li>
    <li>Optional holidays: 3 per year (subject to approval)</li>
    <li>Working on holidays requires prior approval</li>
</ul>

<h3>7. LEAVE ENCASHMENT</h3>
<ul>
    <li>Only earned leave can be encashed</li>
    <li>Maximum 15 days per year can be encashed</li>
    <li>Encashment at the time of resignation/retirement</li>
    <li>Encashment at basic salary rate</li>
</ul>

<h3>8. UNAUTHORIZED ABSENCE</h3>
<ul>
    <li>Absence without approval considered unauthorized</li>
    <li>Loss of pay for unauthorized absence</li>
    <li>Disciplinary action may be taken</li>
    <li>Continuous unauthorized absence for 3+ days may lead to termination</li>
</ul>

<h3>9. ACKNOWLEDGMENT</h3>
<p>I have read and understood the Leave Policy and agree to comply with all leave rules and procedures.</p>';
    }

    private static function poshPolicyTemplate()
    {
        return '<h2>PREVENTION OF SEXUAL HARASSMENT (POSH) POLICY</h2>

<h3>1. POLICY STATEMENT</h3>
<p>AKALP Techno Media Solutions is committed to providing a workplace free from sexual harassment. This policy is in compliance with the Sexual Harassment of Women at Workplace (Prevention, Prohibition and Redressal) Act, 2013.</p>

<h3>2. SCOPE</h3>
<p>This policy applies to all employees, including permanent, temporary, contractual, trainees, and visitors at the workplace.</p>

<h3>3. DEFINITION OF SEXUAL HARASSMENT</h3>
<p>Sexual harassment includes any unwelcome sexually determined behavior, whether directly or by implication:</p>

<h4>3.1 Physical Contact and Advances</h4>
<ul>
    <li>Unwelcome physical contact or advances</li>
    <li>Physical confinement against will</li>
    <li>Unwelcome touching, patting, pinching</li>
</ul>

<h4>3.2 Verbal Harassment</h4>
<ul>
    <li>Sexually colored remarks</li>
    <li>Jokes or comments of sexual nature</li>
    <li>Requests or demands for sexual favors</li>
    <li>Sexually degrading words</li>
</ul>

<h4>3.3 Non-Verbal Harassment</h4>
<ul>
    <li>Showing pornography or sexual images</li>
    <li>Leering or staring</li>
    <li>Sexually suggestive gestures</li>
    <li>Display of sexually offensive material</li>
</ul>

<h4>3.4 Digital Harassment</h4>
<ul>
    <li>Sending sexually explicit emails or messages</li>
    <li>Sharing inappropriate images or videos</li>
    <li>Making sexual comments on social media</li>
</ul>

<h3>4. INTERNAL COMPLAINTS COMMITTEE (ICC)</h3>
<h4>4.1 Constitution</h4>
<ul>
    <li>Presiding Officer: Senior woman employee</li>
    <li>Two members from amongst employees</li>
    <li>One external member from NGO or familiar with sexual harassment issues</li>
    <li>At least 50% members shall be women</li>
</ul>

<h4>4.2 Tenure</h4>
<ul>
    <li>Members serve for a period of 3 years</li>
    <li>No member can serve for more than 2 consecutive terms</li>
</ul>

<h3>5. COMPLAINT PROCEDURE</h3>
<h4>5.1 Filing a Complaint</h4>
<ul>
    <li>Complaint to be filed within 3 months of incident</li>
    <li>Extended period of 3 months if circumstances prevented filing</li>
    <li>Written complaint to ICC with details of incident</li>
    <li>Complaint can be filed in person or via email</li>
</ul>

<h4>5.2 Complaint Details</h4>
<ul>
    <li>Name and address of complainant</li>
    <li>Name and address of respondent</li>
    <li>Details of incident(s) with dates and times</li>
    <li>Names of witnesses, if any</li>
    <li>Supporting documents, if available</li>
</ul>

<h3>6. INQUIRY PROCESS</h3>
<h4>6.1 Initial Assessment</h4>
<ul>
    <li>ICC to acknowledge complaint within 7 days</li>
    <li>Preliminary inquiry to determine if case falls under POSH</li>
    <li>Conciliation may be attempted if complainant requests</li>
</ul>

<h4>6.2 Investigation</h4>
<ul>
    <li>Inquiry to be completed within 90 days</li>
    <li>Both parties given opportunity to present their case</li>
    <li>Witnesses examined if required</li>
    <li>Confidentiality maintained throughout</li>
</ul>

<h4>6.3 Interim Relief</h4>
<ul>
    <li>Transfer of complainant or respondent</li>
    <li>Grant of leave to complainant</li>
    <li>Restraining order against respondent</li>
</ul>

<h3>7. INQUIRY REPORT</h3>
<ul>
    <li>Report submitted within 10 days of completion</li>
    <li>Recommendations for action if harassment proved</li>
    <li>Copy provided to both parties</li>
    <li>Action to be taken within 60 days</li>
</ul>

<h3>8. PENALTIES</h3>
<p>If sexual harassment is proved, the following actions may be taken:</p>
<ul>
    <li>Written apology</li>
    <li>Warning or reprimand</li>
    <li>Withholding of promotion or increment</li>
    <li>Suspension without pay</li>
    <li>Termination of employment</li>
    <li>Deduction from salary for compensation to complainant</li>
</ul>

<h3>9. FALSE COMPLAINTS</h3>
<ul>
    <li>Malicious complaints will be treated seriously</li>
    <li>Action taken against complainant if complaint found false</li>
    <li>Genuine complaints made in good faith will not be penalized even if not proved</li>
</ul>

<h3>10. CONFIDENTIALITY</h3>
<ul>
    <li>All proceedings kept confidential</li>
    <li>Identity of complainant, respondent, and witnesses protected</li>
    <li>Breach of confidentiality subject to disciplinary action</li>
</ul>

<h3>11. PROTECTION AGAINST RETALIATION</h3>
<ul>
    <li>No retaliation against complainant or witnesses</li>
    <li>Any retaliation will be treated as misconduct</li>
    <li>Complainant can report retaliation to ICC</li>
</ul>

<h3>12. AWARENESS AND TRAINING</h3>
<ul>
    <li>Regular awareness programs on POSH</li>
    <li>Training for ICC members</li>
    <li>Policy displayed at prominent places</li>
    <li>Annual report on POSH submitted to authorities</li>
</ul>

<h3>13. CONTACT DETAILS</h3>
<p><strong>Internal Complaints Committee:</strong></p>
<p>Email: icc@akalptechnomediasolutions.com</p>
<p>Phone: +91 8085504485</p>

<h3>14. ACKNOWLEDGMENT</h3>
<p>I acknowledge that I have read and understood the POSH Policy and commit to maintaining a harassment-free workplace.</p>';
    }

    private static function ndaPolicyTemplate()
    {
        return '<h2>NON-DISCLOSURE AGREEMENT (NDA) POLICY</h2>

<h3>1. PURPOSE</h3>
<p>This Non-Disclosure Agreement (NDA) is designed to protect confidential and proprietary information of AKALP Techno Media Solutions and its clients.</p>

<h3>2. PARTIES</h3>
<p>This agreement is between:</p>
<ul>
    <li><strong>Disclosing Party:</strong> AKALP Techno Media Solutions</li>
    <li><strong>Receiving Party:</strong> Employee (as named in this document)</li>
</ul>

<h3>3. DEFINITION OF CONFIDENTIAL INFORMATION</h3>
<p>Confidential Information includes, but is not limited to:</p>

<h4>3.1 Business Information</h4>
<ul>
    <li>Business plans and strategies</li>
    <li>Financial information and projections</li>
    <li>Marketing and sales strategies</li>
    <li>Customer lists and databases</li>
    <li>Supplier and vendor information</li>
    <li>Pricing information and cost structures</li>
</ul>

<h4>3.2 Technical Information</h4>
<ul>
    <li>Source code and software</li>
    <li>Algorithms and processes</li>
    <li>Technical specifications and designs</li>
    <li>Research and development projects</li>
    <li>Patents, trademarks, and copyrights</li>
    <li>Trade secrets and know-how</li>
</ul>

<h4>3.3 Client Information</h4>
<ul>
    <li>Client names and contact details</li>
    <li>Project details and requirements</li>
    <li>Client business information</li>
    <li>Contracts and agreements</li>
    <li>Client feedback and communications</li>
</ul>

<h4>3.4 Employee Information</h4>
<ul>
    <li>Employee personal data</li>
    <li>Salary and compensation details</li>
    <li>Performance evaluations</li>
    <li>Internal communications</li>
</ul>

<h3>4. OBLIGATIONS OF RECEIVING PARTY</h3>
<h4>4.1 Non-Disclosure</h4>
<ul>
    <li>Not disclose confidential information to any third party</li>
    <li>Not use confidential information for personal benefit</li>
    <li>Protect confidential information with reasonable care</li>
    <li>Limit access to confidential information on need-to-know basis</li>
</ul>

<h4>4.2 Use of Information</h4>
<ul>
    <li>Use confidential information only for authorized business purposes</li>
    <li>Not reproduce or copy confidential information without permission</li>
    <li>Not reverse engineer or attempt to derive source code</li>
</ul>

<h4>4.3 Security Measures</h4>
<ul>
    <li>Use strong passwords and authentication</li>
    <li>Encrypt sensitive data</li>
    <li>Lock computers and devices when unattended</li>
    <li>Report any security breaches immediately</li>
    <li>Follow company IT security policies</li>
</ul>

<h3>5. EXCEPTIONS</h3>
<p>Confidential Information does not include information that:</p>
<ul>
    <li>Is publicly available through no fault of receiving party</li>
    <li>Was known to receiving party before disclosure</li>
    <li>Is independently developed by receiving party</li>
    <li>Is received from a third party without breach of obligation</li>
    <li>Is required to be disclosed by law or court order</li>
</ul>

<h3>6. RETURN OF INFORMATION</h3>
<p>Upon termination of employment or upon request:</p>
<ul>
    <li>Return all confidential information and materials</li>
    <li>Delete all electronic copies of confidential information</li>
    <li>Certify in writing that all information has been returned/destroyed</li>
    <li>Return company property including laptops, phones, documents</li>
</ul>

<h3>7. DURATION OF OBLIGATION</h3>
<ul>
    <li>Obligations continue during employment</li>
    <li>Obligations continue for 3 years after termination of employment</li>
    <li>Trade secrets protected indefinitely</li>
    <li>Obligations survive termination of employment</li>
</ul>

<h3>8. INTELLECTUAL PROPERTY</h3>
<h4>8.1 Work Product</h4>
<ul>
    <li>All work created during employment belongs to company</li>
    <li>Employee assigns all rights to company</li>
    <li>Includes inventions, designs, code, documents</li>
    <li>Employee waives moral rights to work product</li>
</ul>

<h4>8.2 Prior Inventions</h4>
<ul>
    <li>Employee to disclose any prior inventions</li>
    <li>Prior inventions excluded from company ownership</li>
    <li>List of prior inventions attached to agreement</li>
</ul>

<h3>9. NON-COMPETE CLAUSE</h3>
<ul>
    <li>Employee shall not work for direct competitors during employment</li>
    <li>Non-compete period: 1 year after termination</li>
    <li>Geographic restriction: India</li>
    <li>Applies to similar roles and responsibilities</li>
</ul>

<h3>10. NON-SOLICITATION</h3>
<ul>
    <li>Not solicit company employees for 2 years after termination</li>
    <li>Not solicit company clients for 2 years after termination</li>
    <li>Not interfere with company business relationships</li>
</ul>

<h3>11. BREACH AND REMEDIES</h3>
<h4>11.1 Consequences of Breach</h4>
<ul>
    <li>Immediate termination of employment</li>
    <li>Legal action for damages</li>
    <li>Injunctive relief to prevent further disclosure</li>
    <li>Recovery of legal costs and expenses</li>
</ul>

<h4>11.2 Reporting Breaches</h4>
<ul>
    <li>Report any suspected breach immediately</li>
    <li>Cooperate in investigation of breaches</li>
    <li>Take corrective action as directed</li>
</ul>

<h3>12. PERMITTED DISCLOSURES</h3>
<ul>
    <li>Disclosure to authorized company personnel</li>
    <li>Disclosure required by law (with prior notice to company)</li>
    <li>Disclosure with written consent of company</li>
</ul>

<h3>13. THIRD PARTY INFORMATION</h3>
<ul>
    <li>Respect confidentiality of third party information</li>
    <li>Not disclose third party information without authorization</li>
    <li>Follow third party confidentiality agreements</li>
</ul>

<h3>14. GOVERNING LAW</h3>
<ul>
    <li>Agreement governed by laws of India</li>
    <li>Jurisdiction: Courts of Bhopal, Madhya Pradesh</li>
    <li>Disputes resolved through arbitration if possible</li>
</ul>

<h3>15. ENTIRE AGREEMENT</h3>
<ul>
    <li>This agreement constitutes entire understanding</li>
    <li>Supersedes all prior agreements</li>
    <li>Amendments must be in writing</li>
    <li>Severability clause applies</li>
</ul>

<h3>16. ACKNOWLEDGMENT AND ACCEPTANCE</h3>
<p>I acknowledge that:</p>
<ul>
    <li>I have read and understood this NDA Policy</li>
    <li>I agree to be bound by all terms and conditions</li>
    <li>I understand the consequences of breach</li>
    <li>I will protect confidential information</li>
    <li>I will comply with all obligations during and after employment</li>
</ul>

<p><strong>By signing below, I accept and agree to all terms of this Non-Disclosure Agreement.</strong></p>';
    }
}
