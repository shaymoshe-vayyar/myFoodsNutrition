import HandleConversion
import requests
from bs4 import BeautifulSoup
import re

__nutDailyRecommendedValues__ = dict()
# __UrlName__ = 'file:///C:/Users/ShayMoshe/OneDrive%20-%20vayyar.com/Documents/Personal/%D7%AA%D7%96%D7%95%D7%A0%D7%94/DRI%20Calculator%20Results%20_%20National%20Agricultural%20Library.html'
__FileName__ = 'c:/temp/DRI1.html'
# __UrlName__ = 'https://www.nal.usda.gov/human-nutrition-and-food-safety/dri-calculator'

def Parse():
    with open(__FileName__) as f:
        textToParse = f.read()

    textToParse = textToParse.replace("&amp;","").replace("alpha;","alpha")
    # #  '<td headers="vitamin-a vitamin-recommended-intake">900 mcg</td>'
    # results1 = re.findall(r'<td\sheaders="([\w-]+)[\w\s-]+recommended-intake">"?([\d\.,]+)\s([\w]+)',
    #                       textToParse)
    # # < td     headers = "zinc mineral-recommended-intake essential" > 11     mg < / td >
    # results2 = re.findall(r'<td\sheaders="([\w-]+)[\w\s-]+recommended-intake(\sessential)?">"?([\d\.,]+)\s([\w]+)',
    #                       textToParse)
    #
    # results3 = re.findall(r'<td\sheaders="([\w-]+)[\w\s-]+recommended-intake">"?([\d\.,]+)\s*\-\s*\d+\s([\w]+)',
    #                       textToParse)
    #
    # results = results1 + results2+results3
    #
    results = re.findall(r'<td\sheaders="([\w\d\-\<\>\/]+)[\w\s-]+recommended-intake(\sessential)?">"?([\d\.,]+)(\s*\-\s*\d+)?\s([\w]+)',
                          textToParse)

    results = results+[('vitamin B6', '', '1.3', '', 'mg'),('vitamin B12', '', '2.4', '', 'mcg')]

    dailyRecommendedValues = dict()
    for result in results:
        nutName = result[0]
        if (nutName=="thiamin"):
            nutName="vitamin-b1"
        elif (nutName == 'riboflavin'):
            nutName = "vitamin-b2"
        elif (nutName == 'folate'):
            nutName = "vitamin-b9"
        elif (nutName == 'niacin'):
            nutName = "vitamin-b3"
        elif (nutName == 'pantothenic-acid'):
            nutName = "vitamin-b5"
        nutName = nutName.replace("-","_")
        nutValue = result[2].replace(',','')
        nutUnits = result[4]
        if (nutUnits =='g') or (nutUnits=='grams'):
            nutUnits = 'gram'
        elif (nutUnits =='mg'):
            nutUnits = 'miliGram'
        elif  (nutUnits =='mcg'):
            nutUnits = 'microGram'
        if (nutUnits=='liters'):
            continue
        nutValueInGram = HandleConversion.GramConversionTable[nutUnits]*float(nutValue)
        #print(f'{nutName},{nutValue} {nutUnits}')
        dailyRecommendedValues[nutName]=nutValueInGram

    return dailyRecommendedValues

__nutDailyRecommendedValues__ = Parse()
