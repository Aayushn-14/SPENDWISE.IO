import pdfplumber
import pandas as pd
import mysql.connector
import sys
import os

# Function to extract data from the PDF (you will need to implement this)
def extract_data_from_pdf(pdf_file_path):
    # Implement your PDF data extraction here
    # Extract data as a dictionary, e.g., data = {'Account Name': 'John Doe', 'Account Number': '12345', ...}
    data = {}  # Replace with actual data extraction logic
    return data

if len(sys.argv) < 2:
    print("No PDF file path provided.")
    sys.exit(1)

# Get the uploaded PDF file path from the command-line argument
uploaded_file_path = sys.argv[1]

# Check if the uploaded file exists
if not uploaded_file_path:
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
