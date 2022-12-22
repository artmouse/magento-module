# Faq MFTF3

##  79 Faq specific tests, grouped by purpose, for greater convenience.

### To run tests it is necessary:
- disable Page Builder (Stores > Settings > Configuration > General > Content Management > Advanced Content Tools > Enable Page Builder = No)
- move "FaqMFTF3/Test/Mftf/faqCategory.jpg" file to folder "magento/dev/tests/acceptance/tests/_data".
- move "FaqMFTF3/Test/Mftf/faq_category_import.csv" file to folder "magento/dev/tests/acceptance/tests/_data".
- move "FaqMFTF3/Test/Mftf/faq_question_import.csv" file to folder "magento/dev/tests/acceptance/tests/_data".
  

    Tests group: AmFaq
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaq -r

    Tests group: AmFaqCategory
        Runs tests related to Faq Category.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqCategory -r

    Tests group: AmFaqQuestion
        Runs tests related to Faq Question.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqQuestion -r

    Tests group: AmFaqTag
        Runs tests related to Faq Tag.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqTag -r

    Tests group: AmFaqApi
        Runs tests related to Faq Api requests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqApi -r

    Tests group: AmFaqSearch
        Runs tests related to search requests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqSearch -r

    Tests group: AmFaqImport
        Runs tests related to import categoryes and questions.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqImport -r

    Tests group: AmFaqConfigurations
        Runs tests related to operations with configurations.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqConfigurations -r

    Tests group: AmFaqAskQuestion
        Runs tests related to operations with asking question.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqAskQuestion -r

    Tests group: AmFaqWidget
        Runs tests related to operations with widget.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmFaqWidget -r
