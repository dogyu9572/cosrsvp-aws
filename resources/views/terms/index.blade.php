@php
$gNum = $gNum ?? null;
$gName = $gName ?? "Terms Of Use";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container">
    @include('components.user-header')
    <div class="contents">
    
        <div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
        
        <div class="wbox sh2">
            <div class="board_view">
                <div class="con">
                    <h2>Article 1 (Purpose)</h2>
                    <p>The purpose of these Terms and Conditions shall be to stipulate the rights, obligations, and responsibilities of Cyber Mall and User in the use of internet related service (hereinafter as "Service") provided by Cosmojin Tour internet travel agency(hereinafter as "Mall") operated by Cosmojin Tour.</p>
                    
                    <h2>Article 2 (Definitions)</h2>
                    <p>① "Mall" shall mean the virtual business premises established by Cosmojin Tour to provide travel products (hereinafter as "Product, Etc.") to User through information communication equipment, such as computer, and it also means the business operator of a cyber mall.</p>
                    <p>② "User" shall mean every User that accesses to "Mall" and receives the services provided by "Mall" in accordance with the Terms and Conditions herein.</p>
                    
                    <h2>Article 3 (Stipulation, Revision and Effect of Terms and Conditions)</h2>
                    <p>① The contents of these Terms and Conditions shall be posted on the screen of "Mall" or be made available to User through other methods, and User shall be deemed to have agreed to these Terms and Conditions when User expresses his/her intention to agree thereto.</p>
                    <p>② "Mall" may revise these Terms and Conditions to the extent that such revision does not violate the relevant laws, such as Framework Act on Electronic Transactions, Digital Signature Act, Act on the Consumer Protection in Electronic Commerce, Etc., Act on the Regulation of Terms and Conditions, and other relevant laws and regulations.</p>
                    <p>③ When "Mall" revises these Terms and Conditions, it shall specify the date of application and the reason for revision, and post the current Terms and Conditions together with the revised Terms and Conditions on the initial screen of "Mall" from 7 days before the effective date of revision to the day before the effective date. However, if the revision is unfavorable to User, "Mall" shall give notice thereof through methods, such as, e-mail, e-mail address registered by User, text message, etc., at least 30 days before the effective date of revision. In this case, the revised contents shall be clearly compared and displayed with the previous contents for easy understanding.</p>
                    <p>④ If User does not express his/her intention to refuse the revised Terms and Conditions despite "Mall"'s notice that User may refuse the revised Terms and Conditions or User may be deemed to have refused if User does not express his/her intention to agree within the notice period, User shall be deemed to have agreed to the revised Terms and Conditions. If User expresses his/her intention to refuse the revised Terms and Conditions, "Mall" or User may terminate the service contract.</p>
                    <p>⑤ The matters not stipulated in these Terms and Conditions and the interpretation of these Terms and Conditions shall be governed by Framework Act on Electronic Transactions, Act on the Consumer Protection in Electronic Commerce, Etc., Act on the Regulation of Terms and Conditions, and other relevant laws and regulations, and local and overseas travel standard terms and conditions, etc.</p>
                    
                    <h2>Article 4 (Provision and Change of Service)</h2>
                    <p>① "Mall" shall perform the following business:</p>
                    <ol>
                        <li>Provision of information on travel products and reservation service;</li>
                        <li>Other business designated by "Mall"</li>
                    </ol>
                    <p>② "Mall" may change the contents of service to be provided in the future pursuant to the contract to be entered into with User. In the event of change of the contents, quantity, and price of service, "Mall" shall immediately notify User of the place where the changed contents are posted.</p>
                    <p>③ In the event that "Mall" needs to change the contents of service already provided or to be provided pursuant to the contract with User due to reasons, such as, out of stock of travel products, change of technical specifications, etc., "Mall" shall immediately notify User of the reason.</p>
                    
                    <h2>Article 5 (Suspension of Service)</h2>
                    <p>① "Mall" may temporarily suspend the provision of service in the event of maintenance, inspection, replacement or breakdown of information communication equipment, such as, computer, or interruption of communication, etc.</p>
                    <p>② "Mall" shall compensate User or a third party for damage caused by the temporary suspension of the provision of service due to the reasons set forth in Paragraph ①. However, this shall not apply if "Mall" proves that it is not intentional or negligent.</p>
                    <p>③ In the event of conversion of business item, abandonment of business, integration between companies, etc., "Mall" shall notify User thereof in the method set forth in Article 8, and compensate User in accordance with the conditions originally offered by "Mall."</p>
                    
                    <h2>Article 6 (Membership)</h2>
                    <p>① User shall apply for membership by expressing his/her intention to agree to these Terms and Conditions after filling out the membership information as prescribed by "Mall."</p>
                    <p>② "Mall" shall register as a member a User who has applied for membership as set forth in Paragraph ①, unless the User falls under any of the following Subparagraphs:</p>
                    <ol>
                        <li>If the applicant has lost his/her membership in the past pursuant to Article 7(3) of these Terms and Conditions. However, this shall not apply if 3 years have passed since the loss of membership pursuant to Article 7(3) and "Mall" has approved the re-registration of membership;</li>
                        <li>If there is false, omitted, or mistaken description in the contents of registration;</li>
                        <li>If it is deemed that registering the applicant as a member would significantly impede the technology of "Mall";</li>
                        <li>If the applicant is under 14 years of age</li>
                    </ol>
                    <p>③ The time of establishment of membership contract shall be the time when "Mall"'s approval reaches User.</p>
                    <p>④ If there is any change in the matters registered at the time of membership application, User shall immediately notify "Mall" of the change through methods, such as, modification of member information, e-mail, etc.</p>
                    
                    <h2>Article 7 (Withdrawal from Membership and Loss of Qualification)</h2>
                    <p>① User may request withdrawal from membership to "Mall" at any time, and "Mall" shall immediately process the withdrawal from membership.</p>
                    <p>② If User falls under any of the following Subparagraphs, "Mall" may limit or suspend membership:</p>
                    <ol>
                        <li>If false information is registered at the time of application for membership;</li>
                        <li>If User does not pay the price of goods, etc., purchased by User from "Mall" or other liabilities borne by User in relation to the use of "Mall" or fails to perform other obligations;</li>
                        <li>If User interferes with others' use of "Mall" or steals the information or acts in a manner that disrupts the order of e-commerce;</li>
                        <li>If User uses "Mall" in a manner that violates the laws, these Terms and Conditions, or public order and morals</li>
                    </ol>
                    <p>③ After "Mall" limits or suspends membership, if the same act is repeated more than twice or the cause is not corrected within 30 days, "Mall" may forfeit membership.</p>
                    <p>④ If "Mall" forfeits membership, membership registration shall be cancelled. In this case, "Mall" shall notify User thereof and give User an opportunity to explain before cancellation of membership registration.</p>
                    
                    <h2>Article 8 (Notification to User)</h2>
                    <p>① "Mall" may notify User of any matter through the e-mail address, text message (SMS), etc., provided by User to "Mall."</p>
                    <p>② "Mall" may substitute individual notification with posting on the bulletin board of "Mall" for not less than 7 days in the event of notification to an unspecified number of Users.</p>
                    
                    <h2>Article 9 (Application for Purchase)</h2>
                    <p>① User shall apply for purchase on "Mall" in the following or similar methods, and "Mall" shall provide User with the following information in an easy-to-understand manner to allow User to make an application for purchase:</p>
                    <ol>
                        <li>Search and selection of travel products, etc.;</li>
                        <li>Entry of name, address, telephone number, e-mail address (or mobile phone number), etc.;</li>
                        <li>Confirmation of the contents of terms and conditions, services for which restriction on withdrawal of offer applies, cost burden such as shipping fees, installation fees, etc.;</li>
                        <li>Confirmation of agreement to these Terms and Conditions and consent or rejection of Article 13(1) (Restriction on Collection and Use of Personal Information);</li>
                        <li>Application for purchase of travel products, etc., and confirmation thereof or confirmation of "Mall"'s receipt thereof;</li>
                        <li>Selection of payment method</li>
                    </ol>
                    <p>② "Mall" may request additional information if it is necessary for the purchase and delivery of travel products, etc., in addition to the personal information set forth in Paragraph ①, and User shall respond to such request.</p>
                    <p>③ If the contents stated in the application for purchase do not match the contents actually requested by User, "Mall" shall immediately notify User of the fact, and if "Mall" has already received payment, "Mall" shall process it in accordance with User's intention or cancel the application for purchase.</p>
                    
                    <h2>Article 10 (Establishment of Contract)</h2>
                    <p>① "Mall" may not accept the application for purchase set forth in Article 9 if it falls under any of the following Subparagraphs. However, "Mall" shall notify User of the fact if it enters into a contract with a minor who has not obtained the consent of his/her legal representative:</p>
                    <ol>
                        <li>If there is false, omitted, or mistaken description in the application;</li>
                        <li>If a minor purchases goods and services prohibited by the Juvenile Protection Act, such as, cigarettes and liquors;</li>
                        <li>If it is deemed that accepting other application for purchase would significantly impede the technology of "Mall"</li>
                    </ol>
                    <p>② The contract shall be deemed to be established when "Mall"'s notice of acceptance reaches User in the form of confirmation of receipt set forth in Article 9(1)⑤.</p>
                    <p>③ "Mall"'s expression of acceptance shall include confirmation of User's application for purchase, availability of sale, and correction or cancellation of the application for purchase.</p>
                    
                    <h2>Article 11 (Method of Payment)</h2>
                    <p>The price of travel products, etc., purchased through "Mall" may be paid by any of the following methods. However, "Mall" may not collect any additional fees for any payment method:</p>
                    <ol>
                        <li>Various account transfers such as phone banking, internet banking, and mail banking;</li>
                        <li>Various card payments including prepaid cards, debit cards, and credit cards;</li>
                        <li>Online bank transfer;</li>
                        <li>Electronic money;</li>
                        <li>Payment upon receipt;</li>
                        <li>Points paid by "Mall" or points earned by mileage, etc.;</li>
                        <li>Other electronic payment methods, etc.</li>
                    </ol>
                    
                    <h2>Article 12 (Notice of Receipt, Change and Cancellation of Application for Purchase)</h2>
                    <p>① "Mall" shall notify User of receipt when it receives User's application for purchase.</p>
                    <p>② User may request "Mall" to change or cancel the application for purchase if there is a discrepancy in expression of intention, and "Mall" shall process such request without delay if the request is made before delivery. However, if payment has already been made, the provisions on withdrawal of offer or cancellation of contract shall apply.</p>
                    
                    <h2>Article 13 (Provision of Personal Information, Etc.)</h2>
                    <p>① "Mall" shall collect the minimum personal information necessary for the implementation of purchase contract. "Mall" shall not collect the following personal information without the prior consent of User:</p>
                    <ol>
                        <li>Items necessary for the implementation of purchase contract: name, address, telephone number, e-mail address (or mobile phone number);</li>
                        <li>Items of use: ID, password, date of birth;</li>
                        <li>Items of payment: payment method, card number, bank account number, etc., of the person who made payment</li>
                    </ol>
                    <p>② "Mall" shall not use the personal information collected for purposes other than the following:</p>
                    <ol>
                        <li>Confirmation of application for purchase, delivery or provision of goods, etc.;</li>
                        <li>Confirmation of payment and price settlement;</li>
                        <li>Customer consultation and complaint handling;</li>
                        <li>Delivery of notice</li>
                    </ol>
                    <p>③ "Mall" shall obtain consent from User when it provides personal information to a third party, and shall notify User of the following matters. However, this shall not apply if it is necessary for the implementation of purchase contract and delivery of goods, etc., and the information is provided to a company that handles delivery (hereinafter as "Delivery Company") or a financial company for payment settlement:</p>
                    <ol>
                        <li>Person to whom personal information is provided;</li>
                        <li>Items of personal information provided;</li>
                        <li>Purpose of use of the person to whom personal information is provided;</li>
                        <li>Period of retention and use by the person to whom personal information is provided</li>
                    </ol>
                    
                    <h2>Article 14 (Obligations of "Mall")</h2>
                    <p>① "Mall" shall not act prohibited by the laws and these Terms and Conditions or against public order and morals, and shall do its best to provide travel products and services continuously and stably.</p>
                    <p>② "Mall" shall have a security system for personal information (or credit information) so that User can use internet service safely.</p>
                    <p>③ "Mall" shall be liable to compensate User for damage if "Mall" or its employee causes damage to User in relation to the provision of service in violation of the provisions set forth in Article 14(1).</p>
                    <p>④ "Mall" shall not send commercial e-mails that User does not want.</p>
                    
                    <h2>Article 15 (Obligations of User ID and Password)</h2>
                    <p>① User shall be responsible for the management of his/her ID and password, except in the case of Article 17.</p>
                    <p>② User shall not allow a third party to use his/her ID and password.</p>
                    <p>③ If User recognizes that his/her ID and password are stolen or used by a third party, User shall immediately notify "Mall" thereof and follow "Mall"'s instructions, if any.</p>
                    
                    <h2>Article 16 (Obligations of User)</h2>
                    <p>User shall not do the following acts:</p>
                    <ol>
                        <li>Registration of false information when applying or changing;</li>
                        <li>Stealing of information of others;</li>
                        <li>Change of information posted on "Mall";</li>
                        <li>Transmission or posting of information (computer programs, etc.) other than the information set by "Mall";</li>
                        <li>Infringement of intellectual property rights, such as, copyrights, of "Mall" and a third party;</li>
                        <li>Acts of defaming or interfering with the business of "Mall" and a third party;</li>
                        <li>Disclosure or posting of obscene or violent messages, images, voices, and other information that goes against good public order and morals on "Mall"</li>
                    </ol>
                    
                    <h2>Article 17 (Liability for ID and Password)</h2>
                    <p>"Mall" shall be liable for compensation if User suffers damage due to the management negligence of "Mall" or intentional or negligent act of "Mall" in relation to the management of ID and password. However, this shall not apply if "Mall" proves that it is not intentional or negligent.</p>
                    
                    <h2>Article 18 (Protection of Personal Information)</h2>
                    <p>① "Mall" shall make efforts to protect User's personal information in accordance with the relevant laws.</p>
                    <p>② The protection and use of personal information shall be governed by the relevant laws and "Mall"'s Privacy Policy.</p>
                    <p>③ "Mall" shall not disclose or provide to a third party User's personal information that it has learned in relation to the provision of service, except in the following cases:</p>
                    <ol>
                        <li>If User consents in advance;</li>
                        <li>If there is a request from an investigative agency in accordance with the provisions of the laws or in accordance with the procedures and methods set forth in the laws for the purpose of investigation</li>
                    </ol>
                    
                    <h2>Article 19 (Ownership of Copyright and Restriction on Use)</h2>
                    <p>① The copyright and other intellectual property rights for the works created by "Mall" shall belong to "Mall."</p>
                    <p>② User shall not use the information for which "Mall" has intellectual property rights for commercial purposes by copying, transmitting, publishing, distributing, broadcasting, or other methods without the prior consent of "Mall", or allow a third party to use it.</p>
                    <p>③ "Mall" shall notify User when it uses the copyright belonging to User in accordance with the agreement.</p>
                    
                    <h2>Article 20 (Resolution of Disputes)</h2>
                    <p>① "Mall" shall reflect the legitimate opinions or complaints raised by User and install and operate a processor for compensation of damage in order to resolve User's complaints and opinions.</p>
                    <p>② "Mall" shall prioritize the complaints and opinions submitted by User. However, if it is difficult to promptly process them, "Mall" shall notify User of the reason and processing schedule.</p>
                    <p>③ If a dispute arises between "Mall" and User and a third party dispute resolution agency, such as, the Fair Trade Commission, the Korea Consumer Agency, etc., is involved, "Mall" shall faithfully comply with the results of dispute resolution by the agency.</p>
                    
                    <h2>Article 21 (Jurisdiction and Governing Law)</h2>
                    <p>① Any lawsuit between "Mall" and User shall be governed by the procedures set forth in the Civil Procedure Act.</p>
                    <p>② The laws of Korea shall be applicable to electronic commerce dispute between "Mall" and user.</p>
                    
                    <h2>Article 22 (Special Provisions)</h2>
                    <p>① The matters not stipulated herein shall be governed by Framework Act on Electronic Transactions, Digital Signature Act, Act on the Consumer Protection in Electronic Commerce, Etc., other relevant laws and regulations, and local and overseas travel standard terms and conditions, etc.</p>
                    
                    <h2>Supplementary Provisions</h2>
                    <p>These terms and conditions shall be effective starting from June 1, 2004.</p>
                </div>
            </div>
        </div>
        
    </div>
</div>
@include('components.user-footer')
@endsection
