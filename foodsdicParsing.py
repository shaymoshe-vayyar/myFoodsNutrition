import HandleConversion
import requests
import requests_cache
from bs4 import BeautifulSoup
#from myGui import showTable
import pandas as pd

__flagChachingSite__ = True

def ParseAndFormat(nameOrUrl):
    if (str.find(nameOrUrl,'//')): # URL
        parsedOrgFormatItem = ParseUrl(nameOrUrl)
    else:
        raise Exception('not implemented')

    parsedItem = itemChangeFormat(parsedOrgFormatItem)

    return parsedItem

def itemChangeFormat(parsedOrgFormatItem):
    # Debug
    #HandleConversion.debugPrintUniqueAttrInDataFrame(parsedOrgFormatItem, 'nutUnits')

    parsedItem = None
    for nutName in parsedOrgFormatItem:
        convertedValue = HandleConversion.convertUnitToStandard(parsedOrgFormatItem[nutName]['nutValue'], parsedOrgFormatItem[nutName]['nutUnits'])
        convertedNutName = HandleConversion.convertNutName(nutName)
        if parsedItem is None:
            parsedItem = pd.DataFrame({convertedNutName: convertedValue}, index=['nutValue', 'nutUnits'])
        else:
            parsedItem.insert(0, convertedNutName, convertedValue)

    #
    return parsedItem

def ParseUrl(url):
    if __flagChachingSite__:
        session = requests_cache.CachedSession('cacheUrlName')
        r = session.get(url)
    else:
        session = requests.session()
        r = session.get(url)

    textToParse = r.text
    soup = BeautifulSoup(textToParse, 'html.parser')
    table = soup.find('table', class_='nv-table')
    nutValueTableRows = None
    for nutData in table.find_all('tr'):
        nutDataEnt = nutData.find_all('td')
        if (len(nutDataEnt) > 1):
            nutName = nutDataEnt[0].text
            # m = re.search(r'\((.*?)\)', nutName)
            nutUnits = nutName[nutName.find("(") + 1:nutName.find(")")]
            if nutName.find("("):
                nutNameWOUnits = nutName[:nutName.find("(")].strip() + (nutName[nutName.find(")") + 1:]).strip()
            else:
                nutNameWOUnits = nutName
            nutValue = nutDataEnt[1].get('data-start')
            if (nutValue is None):
                nutValue = 0
            # print("{} = {}, {}".format(nutNameWOUnits,nutValue,nutNameUnits))
            if nutValueTableRows is None:
                nutValueTableRows = pd.DataFrame({nutNameWOUnits: [nutValue, nutUnits]}, index=['nutValue', 'nutUnits'])
            else:
                nutValueTableRows.insert(0,nutNameWOUnits,[nutValue, nutUnits])

            ## Generate Default Display Units Dictionary
            #if (HandleConversion.__nutUnitsConversionToDisplay__[HandleConversion.__tspn__]!=nutUnits):
            #    print("     '{}' : '{}',".format(nutNameWOUnits,nutUnits))

        else:
           for line in nutData.find_all('th'):
                if line.get('id')=='sizeNameTd':
                    if not line.text.find('100 גרם'):
                        print(line)
                        raise Exception('error in parsing, size is not 100 grams!')
    # showTable(['Name', 'Value', 'Units'],nutValueTableRows)
    return nutValueTableRows

