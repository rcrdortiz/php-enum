<?php

namespace Perseo\Platform\Enum;

use Perseo\Platform\Enum\Exception\IllegalArgumentException;
use Perseo\Platform\Enum\Exception\InvalidEnumConstantDefaultValue;
use Perseo\Platform\Test\PerseoTest;

class EnumTest extends PerseoTest
{
    public function testStandard()
    {
        $enumA = StandardTestEnum::TYPE_A();
        $enumA2 = StandardTestEnum::TYPE_A();

        $this->assertEquals($enumA, $enumA2);
        $this->assertEquals($enumA, StandardTestEnum::TYPE_A());
        $this->assertEquals($enumA2, StandardTestEnum::TYPE_A());
        $this->assertNotEquals($enumA, StandardTestEnum::TYPE_B());
    }

    public function test_same_type_for_different_values()
    {
        $enumA = ValueTestEnum::TYPE_A("foo");
        $enumA2 = ValueTestEnum::TYPE_A("bar");

        $this->assertEquals($enumA->type(), $enumA2->type());

    }

    public function test_enum_cant_have_non_string_default_values_for_constants()
    {
        $this->expectException(InvalidEnumConstantDefaultValue::class);

        InvalidTestEnum::TYPE_B();
    }

    public function test_enum_switch_matches_against_enum_constants()
    {
        $enum = StandardTestEnum::TYPE_B();

        switch ($enum) {
            case StandardTestEnum::TYPE_B:
                $result = true;
                break;
            default:
                $result = false;
                break;
        }

        $this->assertTrue($result);
    }

    public function test_valued_enum_switch_matches_against_enum_constants()
    {
        $enum = ValueTestEnum::TYPE_B("anyValue");

        switch ($enum) {
            case ValueTestEnum::TYPE_B:
                $result = true;
                break;
            default:
                $result = false;
                break;
        }

        $this->assertTrue($result);
    }

    public function test_an_illegalArgumentException_is_thrown_when_the_enum_doesnt_have_the_requested_constant()
    {
        $this->expectException(IllegalArgumentException::class);

        ValueTestEnum::DOESNT_EXISTS();
    }

    public function test_a_valid_enum_is_returned_when_getting_from_value()
    {
        $expected = ValueTestEnum::valueOf(ValueTestEnum::TYPE_B);

        $enum = ValueTestEnum::TYPE_B();

        $this->assertEquals($expected->type(), $enum->type());
    }

    public function test_a_valid_enum_is_returned_when_getting_from_value_with_data()
    {
        $expected = ValueTestEnum::valueOfWithData(ValueTestEnum::TYPE_B, "data");

        $enum = ValueTestEnum::TYPE_B("data");

        $this->assertEquals($expected->type(), $enum->type());
        $this->assertEquals($expected->let(), $enum->let());
    }

    public function test_an_illegalArgumentException_is_thrown_when_the_enum_doesnt_have_the_from_value()
    {
        $this->expectException(IllegalArgumentException::class);

        ValueTestEnum::valueOfWithData("DOESNT_EXISTS", "data");
    }

    public function test_the_default_enum_is_returned_when_the_enum_doesnt_have_the_from_value()
    {
        $data = "anyValue";
        $default = ValueTestEnum::TYPE_B($data);

        $result = ValueTestEnum::valueOfWithData("DOESNT_EXISTS", $data, $default);

        $this->assertEquals($default, $result);
    }

    public function test_the_default_enum_is_returned_when_the_enum_doesnt_have_the_from_value_and_data_is_null()
    {
        $default = StandardTestEnum::TYPE_B();

        $result = StandardTestEnum::valueOf("DOESNT_EXISTS", $default);

        $this->assertEquals($default, $result);
    }

    public function test_get_values_from_enum()
    {
        $expectedValues = [
            StandardTestEnum::TYPE_A(),
            StandardTestEnum::TYPE_B(),
        ];

        $result = StandardTestEnum::values();

        $this->assertEquals($expectedValues, $result);
    }
}

/**
 * @method static StandardTestEnum TYPE_A()
 * @method static StandardTestEnum TYPE_B()
 */
class StandardTestEnum extends Enum
{
    const TYPE_A = "1";
    const TYPE_B = "2";
}

/**
 * @method static ValueTestEnum TYPE_A(mixed $value = null)
 * @method static ValueTestEnum TYPE_B(mixed $value = null)
 */
class ValueTestEnum extends Enum
{
    const TYPE_A = "1";
    const TYPE_B = "2";
}

/**
 * @method static ValueTestEnum TYPE_A()
 * @method static ValueTestEnum TYPE_B()
 */
class InvalidTestEnum extends Enum
{
    const TYPE_A = 1;
    const TYPE_B = 2;
}