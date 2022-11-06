<?php

namespace Unit;

use Zploited\Identity\Client\Models\AccessToken;
use Zploited\Identity\Client\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function testCanExtractProperties()
    {
        $token = new AccessToken("eyJ0eXAiOiJhdCtqd3QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik5IR0hGT2VMcXRoQk0yWlMifQ.eyJpc3MiOiJhcHB3YXJkLmF1dGgtZGV2LmFwcHdhcmQubmV0IiwiYXVkIjoieHNyeEVZbTJ4eXFTTHNaSEN6RUZYZVNuTDVHUDJJekkiLCJqdGkiOiIxNGM0NjQ2YzU5ZWU4YmI0NjM0OWI0MWNlOWIyZDM3ZjNkODk5YWVmYzdmYWJjZTdiODVlMTE3NDdiN2RlNjI5NTYwYWJkMjM5YjMwMzBjZiIsImlhdCI6MTY2NzczOTEwNC45ODc0NDgsIm5iZiI6MTY2NzczOTEwNC45ODc0NSwiZXhwIjoxNjY3NzQyNzA0LjkzNjk1NSwic3ViIjoiMSIsInNjb3BlcyI6WyJvcGVuaWQiLCJlbWFpbCJdfQ.BNqOCmYQqgM582V3Faws6BZMBEyzQp1wa9oaDQ8YCv6SlyiVH_VvEjZyUNjdXK94n2yDIny1SvVYXl3R9iUhfLiPWGowj_fo2SV1y_52qsZaUXdQBDGVG1Ub17a3y-pW6JDwRKemn8Fg9KEvlJ1mDpLMzb8m35oJ6KwBkf3ViqoVg8J8ukmdkYKfvFnfQerUo4mcvDx_W-w9ZXSOEXFH2OaYbtA6IVB-pu51tjtt_jzxg4OEDeHwC9G5M7nyHwpvx2_gE5B5ZQtiWNOYCr5xTvVIjlSawNqaAcSOyvKxVm2KRLjMvoZ7Mlj3BaSEu0YJ84a6rerloHNW8ENXmhxvqco5t74o1nxnIAyzmbGRaYEI2cO3x47AJzQdOz-TkpdwhEjYCqq352LIdm8NwdvNLv86LkfbENtkPKn9OJLXYPrUWLBd2krZO7jTTdQqDx2JO03Xa1lfRY9QgqsXkOhhGr6v2ETBgG-i-ybIGsKtQKvVaWOMTOdtsfWFrkcEdApMgQsC8_LCpiq_ooct3o1iFA3IB-InXep0sx0U2P5CriBiOkQ57u7fOlcCQ0WjnzSX6rre39YW25frHWfR3B8W1G11Z3O4H_267IW7o-AwKYi-GYEGxtSk8aVv0fQC-rhIlERC_WL_UNa5VvfQmpq951cDyHYRS4U29l6wtOeXeZI");

        $this->assertEquals('appward.auth-dev.appward.net', $token->iss);
        $this->assertEquals('xsrxEYm2xyqSLsZHCzEFXeSnL5GP2IzI', $token->aud[0]);
    }

    public function testCanBeSerializedToTokenString()
    {
        $jwt = "eyJ0eXAiOiJhdCtqd3QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik5IR0hGT2VMcXRoQk0yWlMifQ.eyJpc3MiOiJhcHB3YXJkLmF1dGgtZGV2LmFwcHdhcmQubmV0IiwiYXVkIjoieHNyeEVZbTJ4eXFTTHNaSEN6RUZYZVNuTDVHUDJJekkiLCJqdGkiOiIxNGM0NjQ2YzU5ZWU4YmI0NjM0OWI0MWNlOWIyZDM3ZjNkODk5YWVmYzdmYWJjZTdiODVlMTE3NDdiN2RlNjI5NTYwYWJkMjM5YjMwMzBjZiIsImlhdCI6MTY2NzczOTEwNC45ODc0NDgsIm5iZiI6MTY2NzczOTEwNC45ODc0NSwiZXhwIjoxNjY3NzQyNzA0LjkzNjk1NSwic3ViIjoiMSIsInNjb3BlcyI6WyJvcGVuaWQiLCJlbWFpbCJdfQ.BNqOCmYQqgM582V3Faws6BZMBEyzQp1wa9oaDQ8YCv6SlyiVH_VvEjZyUNjdXK94n2yDIny1SvVYXl3R9iUhfLiPWGowj_fo2SV1y_52qsZaUXdQBDGVG1Ub17a3y-pW6JDwRKemn8Fg9KEvlJ1mDpLMzb8m35oJ6KwBkf3ViqoVg8J8ukmdkYKfvFnfQerUo4mcvDx_W-w9ZXSOEXFH2OaYbtA6IVB-pu51tjtt_jzxg4OEDeHwC9G5M7nyHwpvx2_gE5B5ZQtiWNOYCr5xTvVIjlSawNqaAcSOyvKxVm2KRLjMvoZ7Mlj3BaSEu0YJ84a6rerloHNW8ENXmhxvqco5t74o1nxnIAyzmbGRaYEI2cO3x47AJzQdOz-TkpdwhEjYCqq352LIdm8NwdvNLv86LkfbENtkPKn9OJLXYPrUWLBd2krZO7jTTdQqDx2JO03Xa1lfRY9QgqsXkOhhGr6v2ETBgG-i-ybIGsKtQKvVaWOMTOdtsfWFrkcEdApMgQsC8_LCpiq_ooct3o1iFA3IB-InXep0sx0U2P5CriBiOkQ57u7fOlcCQ0WjnzSX6rre39YW25frHWfR3B8W1G11Z3O4H_267IW7o-AwKYi-GYEGxtSk8aVv0fQC-rhIlERC_WL_UNa5VvfQmpq951cDyHYRS4U29l6wtOeXeZI";
        $token = new AccessToken($jwt);

        $this->assertEquals($jwt, (string)$token);
    }

    public function testIsExpired()
    {
        $jwt = "eyJ0eXAiOiJhdCtqd3QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik5IR0hGT2VMcXRoQk0yWlMifQ.eyJpc3MiOiJhcHB3YXJkLmF1dGgtZGV2LmFwcHdhcmQubmV0IiwiYXVkIjoieHNyeEVZbTJ4eXFTTHNaSEN6RUZYZVNuTDVHUDJJekkiLCJqdGkiOiIxNGM0NjQ2YzU5ZWU4YmI0NjM0OWI0MWNlOWIyZDM3ZjNkODk5YWVmYzdmYWJjZTdiODVlMTE3NDdiN2RlNjI5NTYwYWJkMjM5YjMwMzBjZiIsImlhdCI6MTY2NzczOTEwNC45ODc0NDgsIm5iZiI6MTY2NzczOTEwNC45ODc0NSwiZXhwIjoxNjY3NzQyNzA0LjkzNjk1NSwic3ViIjoiMSIsInNjb3BlcyI6WyJvcGVuaWQiLCJlbWFpbCJdfQ.BNqOCmYQqgM582V3Faws6BZMBEyzQp1wa9oaDQ8YCv6SlyiVH_VvEjZyUNjdXK94n2yDIny1SvVYXl3R9iUhfLiPWGowj_fo2SV1y_52qsZaUXdQBDGVG1Ub17a3y-pW6JDwRKemn8Fg9KEvlJ1mDpLMzb8m35oJ6KwBkf3ViqoVg8J8ukmdkYKfvFnfQerUo4mcvDx_W-w9ZXSOEXFH2OaYbtA6IVB-pu51tjtt_jzxg4OEDeHwC9G5M7nyHwpvx2_gE5B5ZQtiWNOYCr5xTvVIjlSawNqaAcSOyvKxVm2KRLjMvoZ7Mlj3BaSEu0YJ84a6rerloHNW8ENXmhxvqco5t74o1nxnIAyzmbGRaYEI2cO3x47AJzQdOz-TkpdwhEjYCqq352LIdm8NwdvNLv86LkfbENtkPKn9OJLXYPrUWLBd2krZO7jTTdQqDx2JO03Xa1lfRY9QgqsXkOhhGr6v2ETBgG-i-ybIGsKtQKvVaWOMTOdtsfWFrkcEdApMgQsC8_LCpiq_ooct3o1iFA3IB-InXep0sx0U2P5CriBiOkQ57u7fOlcCQ0WjnzSX6rre39YW25frHWfR3B8W1G11Z3O4H_267IW7o-AwKYi-GYEGxtSk8aVv0fQC-rhIlERC_WL_UNa5VvfQmpq951cDyHYRS4U29l6wtOeXeZI";
        $token = new AccessToken($jwt);

        $this->assertTrue($token->isExpired());
    }
}