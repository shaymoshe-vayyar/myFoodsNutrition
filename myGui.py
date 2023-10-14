## GUI
import HandleConversion
import PySimpleGUI as psg
import parsingEngine
from datetime import date, timedelta, datetime

### https://stackoverflow.com/questions/32289175/list-of-all-tkinter-events
### https://stackoverflow.com/questions/68528274/how-to-raise-an-event-when-enter-is-pressed-into-an-inputtext-in-pysimplegui
### https://www.blog.pythonlibrary.org/2022/01/25/pysimplegui-an-intro-to-laying-out-elements/
### https://www.tutorialspoint.com/pysimplegui/pysimplegui_progressbar_element.htm

# Input Name of food [TEXTBOX SELECTION]:
# Selected Item
# Table:
#   [Name, Value, Unit]

####
# import time
# colPSG = psg.ProgressBar(100, orientation='h', expand_x=True, size=(20, 20),  key='-PBAR-')
# layout = [
#    [psg.Text('גרם', enable_events=True, font=('Arial', 12), justification='center', expand_x=True),psg.Text('26', key='-OUT-', enable_events=True, font=('Arial Bold', 16), justification='center', expand_x=True)],
#    [colPSG],
#    [psg.Text('שומנים', enable_events=True, font=('Arial', 12), justification='center', expand_x=True)],
#    [psg.Text('76%', key='-OUT-', enable_events=True, font=('Arial Bold', 12), justification='center', expand_x=True)]
# ]
# window = psg.Window('Progress Bar', layout, size=(715, 150))
# while True:
#    event, values = window.read()
#    print(event, values)
#    if event == 'Test':
#       window['Test'].update(disabled=True)
#       for i in range(100):
#          window['-PBAR-'].update(current_count=i + 1)
#          window['-OUT-'].update(str(i + 1))
#          time.sleep(1)
#          window['Test'].update(disabled=False)
#    if event == 'Cancel':
#       window['-PBAR-'].update(max=100)
#    if event == psg.WIN_CLOSED or event == 'Exit':
#       break
# window.close()
####

def GuiFoodData(DBItems):
    itemNamesList = []
    for itemName in DBItems:
        itemNamesList.append(itemName)
    toprow = ['יחידות', 'ערך', 'סימון תזונתי']
    rows = [[]]
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    #lst = psg.Combo(itemNamesList, font=('Arial Bold', 14), expand_x=True, enable_events=True, readonly=False, key="_COMBOINPUT_")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='right',
                     key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],[tbl1]]
    # [lst],
    window = psg.Window("צפיה בערכים תזונתיים למוצר", layout, size=(800, 800), resizable=True,element_justification="right",finalize=True)
    window["_COMBOINPUT_"].Widget.configure(justify="right")
    selItem = None

    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       if event == psg.WIN_CLOSED:
          break
       if event == '_COMBOINPUT_':
           curValue = values['_COMBOINPUT_']
           cntFound = 0
           for itemName in itemNamesList:
               if itemName.find(curValue)>=0:
                   selItem = itemName
                   cntFound = cntFound+1
           if cntFound > 1: # No one selection
               selItem = None
           else:
               print(selItem)
       if event == "Submit":
           listNutForTable = []
           if selItem is not None:
               selItemNutritionValues = DBItems[selItem][parsingEngine.__nutritionValues__]
               for nutName in selItemNutritionValues:
                   nutElem = selItemNutritionValues[nutName]
                   # TODO: change 'nutValue' and 'nutUnits' to constants
                   # TODO: Add selection box for multiple items
                   if (nutName!='כפיות סוככפיות סוכר'):
                      convertedValue = HandleConversion.convertUnitFromStandard([nutElem['nutValue'], nutElem['nutUnits']],
                                                                                HandleConversion.__dictNutNameToUnitsForDisplay__[nutName])
                   #convertedNutName = HandleConversion.convertNutName(nutName)

                   # Trim to 3 last digits
                   convertedValue[0] = round(convertedValue[0]*1000)/1000

                   listNutForTable.append([
                       convertedValue[1], # Units
                       convertedValue[0], # Value
                       nutName
                       ])
               tbl1.update(listNutForTable)
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()

def GuiFoodDailyManager(DBItems, DBDailyData):
    toprow = ['יחידות', 'ערך', 'סימון תזונתי']
    rows = [[]]
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    #lst = psg.Combo(itemNamesList, font=('Arial Bold', 14), expand_x=True, enable_events=True, readonly=False, key="_COMBOINPUT_")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='right',
                     key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    __DateFormat__ = "%Y/%m/%d"

    CalenderGuiElement = psg.Column([[psg.Button(psg.SYMBOL_LEFT_ARROWHEAD, pad=((0, 0), 3), key="-DATELEFT-"),
     psg.Button(date.today().strftime(__DateFormat__), expand_y=True, key="-DATEVALUE-"),
     psg.Button(psg.SYMBOL_RIGHT_ARROWHEAD, pad=((0, 0), 3), key="-DATERIGHT-")]],justification="center")

    DayDataSummaryGuiElement = psg.Column([[

    ]])
    layout = [[CalenderGuiElement],
            [psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],[tbl1]]
    # [lst],
    window = psg.Window("צפיה בערכים תזונתיים למוצר", layout, size=(800, 800), resizable=True,element_justification="right",finalize=True)
    window["-DATEVALUE-"].Widget.configure(justify="center")
    selItem = None

    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       if event == psg.WIN_CLOSED:
          break
       ### Handle Calender
       if event == "-DATELEFT-":  # event == "<Left>":
           oldDateStr = window["-DATEVALUE-"].get_text()
           newDateStr = (datetime.strptime(oldDateStr, __DateFormat__) - timedelta(days=1)).strftime(__DateFormat__)
           window["-DATEVALUE-"].update(newDateStr)
       if event == "-DATERIGHT-":  # event == "<Left>":
           oldDateStr = window["-DATEVALUE-"].get_text()
           newDateStr = (datetime.strptime(oldDateStr, __DateFormat__) + timedelta(days=1)).strftime(__DateFormat__)
           window["-DATEVALUE-"].update(newDateStr)
       elif event == '-DATEVALUE-':
           entry = window["-DATEVALUE-"].widget
           location = entry.winfo_rootx(), entry.winfo_rooty() + entry.winfo_height()
           chosen_mon_day_year = psg.popup_get_date(location=location, close_when_chosen=True)
           if chosen_mon_day_year:
               month, day, year = chosen_mon_day_year
               window["-DATEVALUE-"].update(f'{year}/{month:0>2d}/{day:0>2d}')
       ###
       if event == '_COMBOINPUT_':
           curValue = values['_COMBOINPUT_']
           cntFound = 0
           for itemName in itemNamesList:
               if itemName.find(curValue)>=0:
                   selItem = itemName
                   cntFound = cntFound+1
           if cntFound > 1: # No one selection
               selItem = None
           else:
               print(selItem)
       if event == "Submit":
           listNutForTable = []
           if selItem is not None:
               selItemNutritionValues = DBItems[selItem][parsingEngine.__nutritionValues__]
               for nutName in selItemNutritionValues:
                   nutElem = selItemNutritionValues[nutName]
                   # TODO: change 'nutValue' and 'nutUnits' to constants
                   # TODO: Add selection box for multiple items
                   if (nutName!='כפיות סוככפיות סוכר'):
                      convertedValue = HandleConversion.convertUnitFromStandard([nutElem['nutValue'], nutElem['nutUnits']],
                                                                                HandleConversion.__dictNutNameToUnitsForDisplay__[nutName])
                   #convertedNutName = HandleConversion.convertNutName(nutName)

                   # Trim to 3 last digits
                   convertedValue[0] = round(convertedValue[0]*1000)/1000

                   listNutForTable.append([
                       convertedValue[1], # Units
                       convertedValue[0], # Value
                       nutName
                       ])
               tbl1.update(listNutForTable)
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()


def showTable(toprow,rows):
    #toprow = ['S.No.', 'Name', 'Age', 'Marks']
    #rows = [[1, 'Rajeev', 23, 78],
    #        [2, 'Rajani', 21, 66],
    #        [3, 'Rahul', 22, 60],
    #        [4, 'Robin', 20, 75]]
    psg.set_options(font=("Arial Bold", 14))
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='center', key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[tbl1]]
    window = psg.Window("Table Demo", layout, size=(715, 200), resizable=True)
    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       if event == psg.WIN_CLOSED:
          break
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()

# TODO: Remove
def guiInteractive():
    toprow = ['S.No.', 'Name', 'Age', 'Marks']
    rows = [[1, 'Rajeev', 23, 78],
             [2, 'Rajani', 21, 66],
             [3, 'Rahul', 22, 60],
             [4, 'Robin', 20, 75]]
    psg.set_options(font=("Arial Bold", 14))
    tbl1 = psg.Table(values=rows, headings=toprow,
       auto_size_columns=True,
       display_row_numbers=False,
       justification='center', key='-TABLE-',
       selected_row_colors='red on yellow',
       enable_events=True,
       expand_x=True,
       expand_y=True,
     enable_click_events=True)
    layout = [[psg.Text('Some text on Row 1')],[tbl1],[psg.Text('Enter something on Row 2'),psg.InputText('',enable_events=False, key='-INPUT-')],
              [psg.Button('Submit', visible=False, bind_return_key=True)]]
    window = psg.Window("Table Demo", layout, size=(715, 200), resizable=True)
    while True:
       event, values = window.read()
       print("event:", event, "values:", values)
       #if event == '-INPUT-':
       #    if values['-INPUT-'][-1])
       #    #print(values['-INPUT-'][-1])
       if event == 'Submit':
            print(window['-INPUT-'].get())
       if event == psg.WIN_CLOSED:
          break
       if '+CLICKED+' in event:
          psg.popup("You clicked row:{} Column: {}".format(event[2][0], event[2][1]))
    window.close()
