<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.terms', '# Glittr Terms of Use and Privacy Policy

Glittr is a web application that helps to find and compare bioinformatics training material on GitHub and GitLab. These Terms of Use (“Terms”) apply to the glittr.org website (the “Glittr website”) which is operated and developed by SIB Swiss Institute of Bioinformatics ("SIB", "us", "our"). Your access to, and use of, the Glittr website are subject to the following terms and conditions:

1. Content available. SIB imposes no other restriction on the use or redistributions of the bioinformatics training materials referenced on the Glittr website than those provided by their owners. When accessing and using bioinformatics training material, you must comply with the terms of use of each training material (if applicable) and give the appropriate credits as per the corresponding copyright notice and good scientific practice. The name and logos of the SIB must not be associated with publicity or business promotion without SIB’s prior written approval.

2. User-provided content representations and warranties. By submitting, or otherwise providing bioinformatics training materials listing on Glittr website, you represent and warrant: (i) you have the legal right, authority to submit and share the bioinformatics training materials, and that such submission does not violate any third-party rights, including but not limited to copyrights, trademarks, or any other intellectual property rights; (ii) the bioinformatics training materials are not subject to any contractual restrictions, confidentiality obligations, or any other legal impediments that you prevent you from sharing them on Gittr website.

3. Limited liability. The Glittr website is provided “as is” and “as available”. SIB does not guarantee the correctness, accuracy, reliability and completeness of any bioinformatics training materials referenced on the Glittr website, nor the suitability for any specific purpose. SIB bears no responsibility for any direct, indirect incidental or consequential loss or damages, resulting from the use of the Glittr website. SIB bears no responsibility for the consequences of any temporary or permanent discontinuity of the Glittr website.

4. Data Protection. Through its Privacy Policy, SIB is committed to ensuring your privacy and the confidentiality of your personal data. When accessing and using the Glittr website, SIB only collects the following personal data: IP addresses, date and time of a visit, operating system, browser, name and first name (including your email address) when you contact SIB. Your personal data is only used to provide you with access to the Glittr website, to ensure compliance with the terms of use, to create anonymous usage statistics, and if needed, to answer a request that you might send to SIB. When you access to and use bioinformatics training materials referenced on the Glittr website, please refer to each applicable privacy policy prior to sharing your personal data.

5. Contact. If you have any questions about these Terms, you can contact the SIB Legal and Technology Transfer Office at [legal@sib.swiss](mailto:legal@sib.swiss). We reserve the right to amend and update these Terms at any time.

6. Governing Law and Jurisdiction. These Terms and the use of the Glittr website are governed by Swiss substantive law, without reference to its conflict of laws provisions. The competent courts in Lausanne, Switzerland, have exclusive jurisdiction.

Last revised: 1 March 2024');
    }
};
