@php
$gNum = $gNum ?? null;
$gName = $gName ?? "Privacy Policy";
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
                    <h2>(Relevant Grounds)</h2>
                    <p>Personal Information Protection Act, Article 30 (Establishment and Disclosure of Privacy Policies) and its Enforcement Decree,</p>
                    <p><strong>Article 31 (Contents and Disclosure Methods, Etc. of Privacy Policy)</strong></p>
                    
                    <h3>Article 30 (Establishment and Disclosure of Privacy Policies)</h3>
                    <p>(1) A personal information manager shall establish privacy policies containing the following matters (hereinafter referred to as "Privacy Policies"). In such cases, a public institution shall establish such Privacy Policies for personal information files to be registered pursuant to Article 32:</p>
                    <ol>
                        <li>Purpose for which personal information is managed;</li>
                        <li>Period for which personal information is held and used;</li>
                        <li>Matters concerning providing a third person with personal information (where applicable);</li>
                        <li>Matters concerning entrusting the management of personal information (where applicable);</li>
                        <li>Matters concerning the rights and duties of a subject of information, and how to exercise them;</li>
                        <li>Other matters prescribed by Presidential Decree concerning the management of personal information.</li>
                    </ol>
                    <p>(2) Where a personal information manager establishes or amends the Privacy Policies, he/she shall disclose them in accordance with methods prescribed by Presidential Decree so that a subject of information can readily use them.</p>
                    <p>(3) Where the details of the Privacy policies are inconsistent with those of a contract entered into between a personal information manager and a subject of information, whichever is more advantageous to the subject of information shall govern.</p>
                    <p>(4) The Minister of Security and Public Administration may draw up a guideline for preparing the Privacy Policies and recommend a personal information manager to comply therewith.</p>
                    <p>[Amended on Mar. 23, 2013, Nov 19, 2014]</p>
                    
                    <h3>Enforcement Decree Article 31 (Contents and Disclosure Methods, Etc. of Privacy Policies)</h3>
                    <p>(1) "Other matters as stated in the Presidential Decree" in Article 30(1)(6) shall mean the matters of following Subparagraphs:</p>
                    <ol>
                        <li>Items of personal information to be processed;</li>
                        <li>Matters in relation to destruction of personal information; and</li>
                        <li>Matters in relation to safety measures of personal information subject to Article 30.</li>
                    </ol>
                    <p>(2) The personal information processor shall post continuously the Privacy Policy established or modified pursuant to Article 30(2) of the Act on its website.</p>
                    <p>(3) If it is not possible to post on the website pursuant to Paragraph (2), the personal information processor shall make public the Privacy Policy established or modified in a way of more than one of the following Subparagraphs:</p>
                    <ol>
                        <li>Posting at easily noticeable places of the personal information processor's workplace, etc.;</li>
                        <li>Publishing at the Official Gazette (only in case the personal information processor is the public institution), or general daily newspaper, weekly newsmagazine or internet media subject to Articles 2 (1) a. and c. and (2) of the Act for the Promotion of Newspapers, etc. circulating mainly in over the City and Province where the personal information processor's workplace is located;</li>
                        <li>Publishing at a periodical, newsletter, PR magazine or invoice to be published under the same title at least twice a year and distributed to data subjects on a continual basis; and/or</li>
                        <li>Delivering to the data subject the paper-based agreement entered into between the personal information processor and the data subject so as to supply goods and/or services.</li>
                    </ol>
                    
                    <h3>Act on Promotion of Information and Communications Network Utilization and Information Protection, Etc.,</h3>
                    <p><strong>Article 27-2 (Public Disclosure of Privacy Policy) and its Enforcement Decree,</strong></p>
                    <p><strong>Article 14 (Public Disclosure Methods of Privacy Policy, Etc.)</strong></p>
                    
                    <h3>Article 27-2 (Public Disclosure of Privacy Policy)</h3>
                    <p>(1) Every provider of information and communications services or similar shall, when it handles personal information of users, establish and disclose its privacy policy to the public in a manner specified by Presidential Decree so that users become aware of the policy easily at any time.</p>
                    <p>(2) Privacy policy under paragraph (1) shall include descriptions of all the following matters: [Amended on Feb. 17, 2012]</p>
                    <ol>
                        <li>Purposes of collection and use of personal information, items of personal information collected, and methods of collection;</li>
                        <li>The name of the person (referring to the name of a legal entity, if the person is a legal entity) to whom personal information is furnished, if the personal information is furnished to a third party, purposes of use of the person to whom the personal information is furnished, and items of the personal information furnished;</li>
                        <li>The period of time during which the personal information is possessed and used, and the procedure and method for destruction of the personal information (including the ground for preservation and items of preserved personal information, if it is required to preserve the personal information in accordance with the proviso to the part above subparagraphs of Article 29 (1));</li>
                        <li>Details of business affairs subject to the entrusted handling of personal information and the trustee (they shall be included in the policy on handling, only where this subparagraph is applicable);</li>
                        <li>Rights of users and their legal representatives and methods for the exercise of such rights;</li>
                        <li>Matters concerning installation, operation, and denial of a device that collects personal information automatically, such as an information file for access to internet; and</li>
                        <li>The name and address of the person responsible for management of personal information or the department responsible for business affairs related to the protection of personal information and processing related complaints and other contact information of such person or department.</li>
                    </ol>
                    <p>(3) Every provider of information and communications services or similar shall, when it revises the privacy policy under paragraph (1), give public notice of the reasons for and details of such revision without delay in a manner specified by Presidential Decree, and take measures to make users aware of the details of the revision easily at any time.[Wholly amended on June 13, 2008]</p>
                    
                    <h3>Enforcement Decree, Article 14 (Public Disclosure Methods of Privacy Policy, Etc.)</h3>
                    <p>① Pursuant to Article 27 -2(1) of the Act, information communication service provider, etc. shall public privacy policy via at least one of the methods stated in the following paragraphs by taking into consideration personal information collection location and medium, etc., and it shall be designated as "Privacy Policy." [Amended on Jan 28, 2009]</p>
                    <ol>
                        <li>The method in which a user can see the matters stipulated in the subparagraphs of Article 27-2(2) of the Act on the first screen page of the internet homepage or its connected screen page. In this case, the information communication service provider shall utilize character size, color, etc., to enable a user to easily identify Privacy Policy;</li>
                        <li>The method in which Privacy Policy is attached or provided for the reading in an easily ascertainable place inside of the store or office;</li>
                        <li>The method in which Privacy Policy is continuously published for no less than twice per year and consistently published in periodicals, information booklet, PR material, or request form distributed to a user</li>
                    </ol>
                    <p>② Any change and its specifics relating to Privacy Policy in accordance with Article 27-2(3) of the Act shall be published via any one of the methods in the following subparagraphs [Amended on Jan 28, 2009]</p>
                    <ol>
                        <li>Notice of the first screen page of the internet homepage operated by information communication service provider or additional page disclosure;</li>
                        <li>Notification to a user via writing, facsimile, e-mail or other similar manner; or</li>
                        <li>Attachment or provision in the easily identifiable place inside of the store or office</li>
                    </ol>
                    <p>③ Deleted [2014.11.28]</p>
                    
                    <h2>Privacy Policy</h2>
                    <p>Cosmojin Tour deems personal information protection as very important as follows. Thus, Cosmojin Tour hereby publishes privacy policy as below for the protection of personal information.</p>
                    <p>The purpose of this Privacy policy is to notify the purpose and methods of the use of personal information provided by you in accordance with the relevant laws, such as, "Personal Information Protection Act", and "Act on Promotion of Information and Communications Network Utilization and Information Protection, Etc." as well as the measures taken for the protection of personal information.</p>
                    <p>This Policy may be revised pursuant to the policy change of Cosmojin Tour, and thus, you are well advised to visit here on a regular basis and check whether there is any change.</p>
                    <p>The Privacy Policy of Cosmojin Tour is as follows.</p>
                    
                    <h3>Definitions</h3>
                    <ol>
                        <li>"Cosmojin Tour" shall mean the virtual business premises where goods or services are traded by using information communication equipment, such as computer, so that Cosmojin Tour provides goods or services to a user, or it shall mean the company that operates Cosmojin Tour.</li>
                        <li>"User" shall mean every user that receives the service provided by "Cosmojin Tour" in accordance with the Terms and Conditions herein by accessing to "Cosmojin Tour."</li>
                        <li>"Third Party" shall mean a natural person, company, public institution, government investment institution and others other than user, service provider that collects personal information with the consent from the said user, or entrustment party to which personal information handling is entrusted by service provider.</li>
                    </ol>
                    
                    <h3>1. Personal Information Collection Items and Purposes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Items</th>
                                <th>Purposes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>E-mail address, ID, password, name, date of birth</td>
                                <td>Cosmojin Tour member service subscription, log-in, service use benefits, service provision, etc.</td>
                            </tr>
                            <tr>
                                <td>Name, e-mail address, contact information</td>
                                <td>Product purchase and reservation within Cosmojin Tour</td>
                            </tr>
                            <tr>
                                <td>Card number</td>
                                <td>Product payment within Cosmojin Tour</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h3>2. Personal Information Collection Methods</h3>
                    <p>Cosmojin Tour collects personal information via methods, such as, member subscription or reservation by a user via the website, product purchase, event participation, or generated information collection tool upon homepage use, etc.</p>
                    
                    <h3>3. Personal Information Retention and Use Period</h3>
                    <p>(1) Cosmojin Tour shall keep and use personal information of a customer during his/her service use period, and in the case that a member cancels subscription or the expiry of personal information is due as a member does not utilize the service for a certain period, the collected personal information shall be destructed so that it cannot be read or utilized. Notwithstanding the foregoing, if the pertinent information is needed to be preserved in accordance with the relevant laws and regulations, it shall be kept.</p>
                    <p>(2) If the information is needed to be preserved in accordance with the relevant laws and regulations, such as Commercial Act and Act on the Consumer Protection in Electronic Commerce, Etc. (hereinafter as "Electronic Commerce Act"), Electronic Financial Transactions Act, Specialized Credit Finance Business Act, Framework Act on National Taxes, Corporate Tax Act, and Value-Added Tax Act, the company shall keep member information during the period set forth in the applicable laws and regulations. The Company shall use the kept information only for the designated purpose and the retention period shall be as follows:</p>
                    <ul>
                        <li>A. Preservation period promised individually upon consumer consent</li>
                        <li>B. Subscriber electronic communication date, which is necessary for the provision of the verification data concerning communication, beginning and termination time for electronic communication, subscriber number, such as communication number, and position tracking data of information communication device accessed to the network: 12 months (Protection of Communications Secrets Act)</li>
                        <li>C. Log record data, necessary for the provision of the verification data concerning communication, IP address, etc.: 3 months (Protection of Communications Secrets Act)</li>
                        <li>D. Record on designation and advertisement: 6 months (Electronic Commerce Act)</li>
                        <li>E. Record on contract or subscription revocation, etc.: 5 years (Electronic Commerce Act)</li>
                        <li>F. Record on payment settlement and supply of goods: 5 years (Electronic Commerce Act)</li>
                        <li>G. Record on consumer complaint or dispute handling: 3 years (Electronic Commerce Act)</li>
                        <li>H. Record on credit information collection, processing, and use, etc.: 3 years (Use and Protection of Credit Information Act)</li>
                    </ul>
                    
                    <h3>4. Personal Information Entrustment Management</h3>
                    <p>(1) In order to facilitate the provision of customer services, the company appoints a professional company to entrust a partial portion of the work.</p>
                    <p>(2) To be fully committed to the protection of personal information, the Company shall stipulate, in clear terms, proper handling process on personal information entrustment by service provider, security instructions, confidentiality, restriction on the use beyond the stated purpose or scope, and prohibition on re-entrustment, thereby giving rise to no confusion on the liability of the responsible party upon the occurrence of an accident , and the pertinent contractual relationship shall be made in writing or in an electronic form and kept in strict management and supervision.</p>
                    <p>(3) Any change of the specifics of entrusted work or the entrustment company shall be disclosed without any delay via this Privacy Policy.</p>
                    <p>(4) The status of personal information of a customer entrusted by the company shall be as follows:</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Entrustment Company Name</th>
                                <th>Entrustment Specifics</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>KG Inicis Co., Ltd.</td>
                                <td>Settlement agency work for product payment</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>
@include('components.user-footer')
@endsection
