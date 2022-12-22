# Customer Group Catalog MFTF3

##  36 Customer Group Catalog specific tests, grouped by purpose, for greater convenience.

### To run tests it is necessary:

- disable Page Builder (Stores > Settings > Configuration > General > Content Management > Advanced Content Tools > Enable Page Builder = No)
- move "GroupcatMFTF3/Test/Mftf/amGroupcatImage.jpeg" file to folder "magento/dev/tests/acceptance/tests/_data".


    Tests group: AmGroupCatalog
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmGroupCatalog -r

    Tests group: AmReplacePriceToRequestFormRule
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmReplacePriceToRequestFormRule -r
	
    Tests group: AmHideButton
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmHideButton -r
            
    Tests group: AmHideCategory
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmHideCategory -r

    Tests group: AmHideProduct
        Runs all tests.
        SSH command to run this group of tests:
        vendor/bin/mftf run:group AmHideProduct -r
