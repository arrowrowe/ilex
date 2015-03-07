<?php


use \Ilex\Lib\Validate;


class ValidateTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $data = array(
            'password' => '1234',
            'age' => '23',
        );
        $result = Validate::batch($data, array(
            array(
                'name' => 'username',
                'require' => array('message' => 'NAME_REQUIRED'),
                'length_gt' => array('value' => 0)
            ),
            array(
                'name' => 'password',
                'require' => array('message' => 'PASSWORD_REQUIRED'),
                'length_ge' => array('value' => 6, 'message' => 'PASSWORD_LENGTH_LT_6')
            ),
            array(
                'name' => 'age',
                'require' => array('type' => 'int', 'message' => 'AGE_REQUIRE')
            ),
            array(
                'name' => 'gender',
                'default' => 'male'
            ),
        ));
        $this->assertEquals(array(
            'username' => array('NAME_REQUIRED'),
            'password' => array('PASSWORD_LENGTH_LT_6')
        ), $result, 'Validation result does not come out as expected.');
        $this->assertArrayHasKey('gender', $data, 'Validation\'s default fails.');
        $this->assertEquals('male', $data['gender'], 'Validation\'s default fails.');
        $this->assertSame(23, $data['age'], 'Validation\'s type requirement fails.');
    }
}