import pdfplumber
import mysql.connector
import sys
import os

if len(sys.argv) < 2:
    print("No PDF file path provided.")
    sys.exit(1)

# Get the uploaded PDF file path from the command-line argument
uploaded_file_path = sys.argv[1]

# Check if the uploaded file exists
if not os.path.isfile(uploaded_file_path):
    print("Uploaded file does not exist.")
    sys.exit(1)

# Open the PDF file using pdfplumber
with pdfplumber.open(uploaded_file_path) as pdf:
    # Access the first page
    first_page = pdf.pages[0]

    # Extract text from the PDF
    pdf_text = first_page.extract_text()

# Define the keywords to search for
keywords = ['Account Name', 'Account Number', 'Branch', 'IFS code', 'Email']

# Initialize variables for data fields
account_name_value = None
account_number_value = None
branch_value = None
ifs_code_value = None
email_value = 'NA'  # Default value

# Split the extracted text into lines
pdf_lines = pdf_text.split('\n')

# Iterate through the lines to extract the data
for line in pdf_lines:
    for keyword in keywords:
        if keyword in line:
            parts = line.split(keyword)
            if len(parts) > 1:
                value = parts[1].strip()
                if keyword == 'Account Name':
                    account_name_value = value
                elif keyword == 'Account Number':
                    account_number_value = value
                elif keyword == 'Branch':
                    branch_value = value
                elif keyword == 'IFS code':
                    ifs_code_value = value
                elif keyword == 'Email':
                    email_value = value

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
        sql = "INSERT INTO clients(`Name`, `Bank Name`, `IFSC`, `Account Number`, `Email`) VALUES (%s, %s, %s, %s, %s)"
        values_to_insert = (account_name_value, branch_value, values.get("ifs_code_value", [None])[0], values.get("account_number_value", [None])[0], email_value)
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
