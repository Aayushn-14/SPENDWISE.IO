import pandas as pd
import mysql.connector
import sys
import os

if len(sys.argv) < 2:
    print("No Excel file path provided.")
    sys.exit(1)

# Get the uploaded file path from the command-line argument
uploaded_file_path = sys.argv[1]

# Check if the uploaded file exists
if not os.path.isfile(uploaded_file_path):
    print("Uploaded file does not exist.")
    sys.exit(1)

# Load the Excel file
df = pd.read_excel(uploaded_file_path, header=None)

# Define the keywords to search for
keywords = ['Name', 'Account Number', 'Bank Name', 'IFSC', 'Email']

# Initialize variables for "Name" and "Bank Name"
name_value = None
bank_name_value = None

# Initialize a dictionary to store the values
values = {}

# Iterate through the first 15 lines of the DataFrame
for index, row in df.head(15).iterrows():
    for col_name in df.columns:
        cell_value = str(row[col_name])
        for keyword in keywords:
            if keyword in cell_value:
                # Print the cell name (column name and row index)
                # print(f"Cell Name: {col_name}{index + 1}")

                # Print the value in the next column of the same row
                if col_name + 1 in df.columns:
                    next_col_name = df.columns[df.columns.get_loc(col_name) + 1]
                    next_cell_value = str(row[next_col_name])
                    # print(f"Next Column Value: {next_cell_value}")

                    # Determine if the keyword is "Name" or "Bank Name" and store the values
                    field_name = keyword.strip(':')  # Remove ':' from the keyword
                    if field_name == 'Name':
                        if name_value is None:
                            name_value = next_cell_value
                        else:
                            if bank_name_value is None:
                                bank_name_value = next_cell_value
                    else:
                        values[field_name] = values.get(field_name, [])
                        values[field_name].append(next_cell_value)

# Connect to your MySQL database (replace with your connection details)
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "spendwisedb"
}

conn = mysql.connector.connect(**db_config)

# Check if the connection is successful
if conn.is_connected():
    cursor = conn.cursor()

    try:
        # Insert the values into your MySQL database table
        sql = "INSERT INTO clients (`Name`, `Bank Name`, `Account Number`, `IFSC`, `Email`) VALUES (%s, %s, %s, %s, %s)"
        values_to_insert = (name_value, bank_name_value, values.get("Account Number", [None])[0],
                            values.get("IFSC", [None])[0], values.get("Email", [None])[0])
        cursor.execute(sql, values_to_insert)

        # Commit changes to the database
        conn.commit()
        cursor.close()
        conn.close()
        print("Data inserted into the database table successfully")
    except mysql.connector.Error as err:
        print("MySQL Error:", err)
        conn.rollback()  # Rollback changes if an error occurs
    except Exception as e:
        import traceback
        print("An error occurred:")
        print(traceback.format_exc())
        print("Exception details:", e)
