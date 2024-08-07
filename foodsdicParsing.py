import HandleConversion
import requests
import requests_cache
from bs4 import BeautifulSoup
#from myGui import showTable
import pandas as pd

### https://requests-cache.readthedocs.io/en/stable/
### https://www.reddit.com/r/Python/comments/2eoeji/how_to_turn_a_beautifulsoup_object_into_a_string/
### https://pypi.org/project/chardet/
__flagChachingSite__ = True

__nutDisplayUnitsEng__ = dict()

__ignoreNut__ = 'כפיות סוכר'

def ParseAndFormat(nameOrUrl):
    if (str.find(nameOrUrl,'//')): # URL
        parsedItem = ParseUrl(nameOrUrl)
    else:
        raise Exception('not implemented')

    return parsedItem

def ParseUrl(url : str):
    if (url.find('http') < 0) and (url.lower().startswith('c:\\')):  # Local
        with open(url, 'r',encoding='windows-1255') as f:
            textToParse = f.read()
    else:
        if __flagChachingSite__:
            session = requests_cache.CachedSession('cacheUrlName')
            r = session.get(url)
            if not r.from_cache:
                print(f'{url} not from cache!')
        else:
            session = requests.session()
            r = session.get(url)
        textToParse = r.text

    soup = BeautifulSoup(textToParse, 'html.parser')
    table = soup.find('table', class_='nv-table')
    nutValueTableRows = dict()
    for nutData in table.find_all('tr'):
        nutDataEnt = nutData.find_all('td')
        if (len(nutDataEnt) > 1):
            nutName = nutDataEnt[0].text
            # EngNutName = ''
            # for contentSection in nutDataEnt[0].contents:
            #     if (contentSection.name == 'a'):
            #         linkEng = contentSection.attrs['href']
            #         EngNutName = (linkEng[linkEng.rfind('/')+1:linkEng.rfind('.')]).lower()
            nutUnits = nutName[nutName.find("(") + 1:nutName.find(")")]
            if nutName.find("("):
                nutNameWOUnits = nutName[:nutName.find("(")].strip() + (nutName[nutName.find(")") + 1:]).strip()
            else:
                nutNameWOUnits = nutName
            if nutUnits == 'אנרגיה':  # Special error in the site - Need to switch
                tmp = nutUnits
                nutUnits = nutNameWOUnits
                nutNameWOUnits = tmp
            nutValue = nutDataEnt[-1].text.strip()
            if (nutValue is None) or (len(nutValue)==0):
                nutValue = 0

            if (nutNameWOUnits.find(__ignoreNut__)>=0):
                continue

            if (nutNameWOUnits == 'ויטמין B'):
                nutNameWOUnits = 'סה"כ ויטמין B'
            if (nutNameWOUnits == 'ויטמין B3'):
                nutNameWOUnits = "ויטמין B3 - ניאצין"
            if (nutNameWOUnits == 'מתוכם שומן טראנס'):
                continue  # ignore
            if (nutNameWOUnits == 'אלכוהול'):
                continue  # ignore
            if (nutNameWOUnits == 'נחושת'):
                continue # ignore
            if (nutNameWOUnits == 'תראונין'):
                continue # ignore
            if (nutNameWOUnits == 'ואלין'):
                continue # ignore
            if (nutNameWOUnits == 'מנגן'):
                continue # ignore
            if (nutNameWOUnits == 'לאוצין'):
                continue # ignore
            if (nutNameWOUnits == 'ארגינין'):
                continue # ignore
            if (nutNameWOUnits == 'טריפטופן'):
                continue # ignore
            if (nutNameWOUnits == 'ליזין'):
                continue # ignore
            if (nutNameWOUnits == 'אלנין'):
                continue # ignore
            if (nutNameWOUnits == 'ויטמין B8'):
                continue # ignore
            print(nutNameWOUnits)
            if nutNameWOUnits == 'מתוכן סוכרים':
                nutNameWOUnits = 'סוכרים'
            EngNutName =HandleConversion.__dictHebNameToEngName__[nutNameWOUnits]
            # HandleConversion.__dictEngNameToHebName__[EngNutName] = nutNameWOUnits
            # HandleConversion.__dictHebNameToEngName__[nutNameWOUnits] = EngNutName

            print("{} ({}) = {}, {}".format(nutNameWOUnits,EngNutName,nutValue,nutUnits))
            ## Generate Default Display Units Dictionary
            #if (HandleConversion.__dictEngNameToHebName__[HandleConversion.__tspn__]!=nutUnits):
            #    print("     '{}' : '{}',".format(nutNameWOUnits,nutUnits))

            [convertedValue, convertedUnit] = HandleConversion.convertUnitToStandard(nutValue, nutUnits)

            # TODO: Remove
            #convertedNutName = HandleConversion.convertNutName(nutNameWOUnits)

            # Check units are not changing
            nutEngUnits = HandleConversion.__dictHebNameToEngName__[nutUnits]
            if (__nutDisplayUnitsEng__.__contains__(EngNutName)):
                if (__nutDisplayUnitsEng__[EngNutName] != nutEngUnits):
                    old_unit = __nutDisplayUnitsEng__[EngNutName]
                    print(f'Display unit for {EngNutName} has changed during read from {old_unit} to {nutEngUnits}')
            else:
                __nutDisplayUnitsEng__[EngNutName] = nutEngUnits

            nutValueTableRows['_'+EngNutName] = convertedValue
        else:
           for line in nutData.find_all('th'):
                if line.get('id')=='sizeNameTd':
                    if not line.text.find('100 גרם'):
                        print(line)
                        raise Exception('error in parsing, size is not 100 grams!')

    # showTable(['Name', 'Value', 'Units'],nutValueTableRows)
    return nutValueTableRows

# Save Table
# __dictHebNameToEngName__ = {
#     'אנרגיה'    :   __Cal__,
#     'מק"ג'      :   __microGram__,
#     'מ"ג'       :   __miliGram__,
#     'גרם'       :   __gram__,
#     'כפיות סוכר':   __tspn__
# }               # Partial List to be filled later on
# __dictEngNameToHebName__ = dict()
# for attr in __dictHebNameToEngName__:
#     __dictEngNameToHebName__[__dictHebNameToEngName__[attr]] = attr

# DatabaseHandler.CreateTable('Eng_heb_terms',{'eng_name': 'str','heb_name':'str', 'category' : 'str'})
# for k,v in __dictHebNameToEngName__.items():
#     DatabaseHandler.addItem('Eng_heb_terms',['eng_name','heb_name', 'category' ],[v,k,'nutrition'])

# mylist = [HandleConversion.__Cal__,HandleConversion.__microGram__,HandleConversion.__miliGram__,HandleConversion.__gram__,HandleConversion.__tspn__]
# for name in mylist:
#     DatabaseHandler.updateItem('Eng_heb_terms', 'eng_name', name, ['wunits'], ['category'])
