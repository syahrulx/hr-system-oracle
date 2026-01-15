-- SIMPLIFIED FIX: Add owners to USERS view
-- Run each block separately in Oracle SQL Developer

-- ============================================
-- STEP 1: Add PASSWORD column to OWNER table
-- ============================================
ALTER TABLE OWNER ADD PASSWORD VARCHAR2(255);

-- ============================================
-- STEP 2: Set the hashed password for owners
-- Password: V!t@lity2025
-- ============================================
UPDATE OWNER SET PASSWORD = '$2y$10$mVJq/KSl8wUUX76HsrnAReq7RnqKJ7N3g2u8etOJJq0roX30NiK26';
COMMIT;

-- ============================================
-- STEP 3: Verify the password was set
-- ============================================
SELECT OWNERID, OWNEREMAIL, PASSWORD FROM OWNER;

-- ============================================
-- STEP 4: Drop old view (run as anonymous block)
-- ============================================
BEGIN
   EXECUTE IMMEDIATE 'DROP VIEW USERS';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

-- ============================================
-- STEP 5: Create new USERS view with UNION ALL
-- ============================================
CREATE OR REPLACE VIEW USERS AS
SELECT 
    EMPLOYEEID as USER_ID,
    EMAIL as EMAIL,
    EMPLOYEENAME as NAME,
    PASSWORD as PASSWORD,
    EMPLOYEEROLE as USER_ROLE,
    EMPLOYEEADDRESS as ADDRESS,
    EMPLOYEERATE as RATE,
    EMPLOYEEHIREDON as HIRED_ON,
    ANNUALLEAVEBALANCE as ANNUAL_LEAVE_BALANCE,
    SICKLEAVEBALANCE as SICK_LEAVE_BALANCE,
    EMERGENCYLEAVEBALANCE as EMERGENCY_LEAVE_BALANCE,
    SUPERVISOREMPLOYEEID as SUPERVISOR_ID,
    REMEMBER_TOKEN as REMEMBER_TOKEN,
    EMPLOYEENUMBER as PHONE,
    NULL as IC_NUMBER
FROM EMPLOYEE
UNION ALL
SELECT 
    10000 + OWNERID as USER_ID,
    OWNEREMAIL as EMAIL,
    OWNEREMAIL as NAME,
    PASSWORD as PASSWORD,
    'owner' as USER_ROLE,
    NULL as ADDRESS,
    0 as RATE,
    SYSDATE as HIRED_ON,
    0 as ANNUAL_LEAVE_BALANCE,
    0 as SICK_LEAVE_BALANCE,
    0 as EMERGENCY_LEAVE_BALANCE,
    NULL as SUPERVISOR_ID,
    NULL as REMEMBER_TOKEN,
    NULL as PHONE,
    NULL as IC_NUMBER
FROM OWNER;

-- ============================================
-- STEP 6: Verify owners appear in USERS view
-- ============================================
SELECT USER_ID, EMAIL, USER_ROLE, PASSWORD FROM USERS WHERE LOWER(USER_ROLE) = 'owner';

-- Expected: You should see rows with owner emails and hashed passwords starting with $2y$
