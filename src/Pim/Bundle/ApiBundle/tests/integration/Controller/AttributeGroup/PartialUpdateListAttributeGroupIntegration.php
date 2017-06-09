<?php

namespace Pim\Bundle\ApiBundle\tests\integration\Controller\AttributeGroup;

use Akeneo\Test\Integration\Configuration;
use Pim\Bundle\ApiBundle\Stream\StreamResourceResponse;
use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class PartialUpdateListAttributeGroupIntegration extends ApiTestCase
{
    public function testCreateAndUpdateAListOfAttributeGroups()
    {
        $data =
<<<JSON
{"code": "attributeGroupA","sort_order": 7}
{"code": "technical","sort_order": 8}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"code":"attributeGroupA","status_code":204}
{"line":2,"code":"technical","status_code":201}
JSON;
        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
        $this->assertArrayHasKey('content-type', $httpResponse->headers->all());
        $this->assertSame(StreamResourceResponse::CONTENT_TYPE, $httpResponse->headers->get('content-type'));

        $expectedAttributeGroups = [
            'attributeGroupA' => [
                'code'       => 'attributeGroupA',
                'sort_order' => 7,
                'attributes' => [
                    'sku',
                    'a_date',
                    'a_file',
                    'a_price',
                    'a_price_without_decimal',
                    'a_ref_data_multi_select',
                    'a_ref_data_simple_select',
                    'a_text',
                    'a_regexp',
                    'a_text_area',
                    'a_yes_no',
                    'a_scopable_price',
                    'a_localized_and_scopable_text_area',
                ],
                'labels'     => [
                    'en_US' => 'Attribute group A',
                    'fr_FR' => "Groupe d'attribut A",
                ],
            ],
            'technical'       => [
                'code'       => 'technical',
                'sort_order' => 8,
                'attributes' => [],
                'labels'     => [],
            ],
        ];

        $attributeGroupA = $this->getNormalizedAttributeGroup('attributeGroupA');
        $technical = $this->getNormalizedAttributeGroup('technical');

        $this->assertSame($expectedAttributeGroups['attributeGroupA'], $attributeGroupA);
        $this->assertSame($expectedAttributeGroups['technical'], $technical);
    }

    public function testCreateAndUpdateSameAttributeGroup()
    {
        $data =
<<<JSON
{"code": "industrial"}
{"code": "industrial"}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"code":"industrial","status_code":201}
{"line":2,"code":"industrial","status_code":204}
JSON;

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
    }

    public function testPartialUpdateListWithMaxNumberOfResourcesAllowed()
    {
        $maxNumberResources = $this->getMaxNumberResources();

        for ($i = 0; $i < $maxNumberResources; $i++) {
            $data[] = sprintf('{"code": "my_code_%s"}', $i);
        }
        $data = implode(PHP_EOL, $data);

        for ($i = 0; $i < $maxNumberResources; $i++) {
            $expectedContent[] = sprintf('{"line":%s,"code":"my_code_%s","status_code":201}', $i + 1, $i);
        }
        $expectedContent = implode(PHP_EOL, $expectedContent);

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
    }

    public function testPartialUpdateListWithTooManyResources()
    {
        $client = $this->createAuthenticatedClient();
        $client->setServerParameter('CONTENT_TYPE', StreamResourceResponse::CONTENT_TYPE);

        $maxNumberResources = $this->getMaxNumberResources();

        for ($i = 0; $i < $maxNumberResources + 1; $i++) {
            $data[] = sprintf('{"identifier": "my_code_%s"}', $i);
        }
        $data = implode(PHP_EOL, $data);

        $expectedContent =
<<<JSON
    {
        "code": 413,
        "message": "Too many resources to process, ${maxNumberResources} is the maximum allowed."
    }
JSON;

        $client->request('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertJsonStringEqualsJsonString($expectedContent, $response->getContent());
        $this->assertSame(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $response->getStatusCode());
    }

    public function testPartialUpdateListWithInvalidAndTooLongLines()
    {
        $line = [
            'invalid_json_1'  => str_repeat('a', $this->getBufferSize() - 1),
            'invalid_json_2'  => str_repeat('a', $this->getBufferSize()),
            'invalid_json_3'  => '',
            'line_too_long_1' => '{"code":"foo"}' . str_repeat('a', $this->getBufferSize()),
            'line_too_long_2' => '{"code":"foo"}' . str_repeat(' ', $this->getBufferSize()),
            'line_too_long_3' => str_repeat('a', $this->getBufferSize() + 1),
            'line_too_long_4' => str_repeat('a', $this->getBufferSize() + 2),
            'line_too_long_5' => str_repeat('a', $this->getBufferSize() * 2),
            'line_too_long_6' => str_repeat('a', $this->getBufferSize() * 5),
            'invalid_json_4'  => str_repeat('a', $this->getBufferSize()),
        ];

        $data =
<<<JSON
${line['invalid_json_1']}
${line['invalid_json_2']}
${line['invalid_json_3']}
${line['line_too_long_1']}
${line['line_too_long_2']}
${line['line_too_long_3']}
${line['line_too_long_4']}
${line['line_too_long_5']}
${line['line_too_long_6']}
${line['invalid_json_4']}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"status_code":400,"message":"Invalid json message received"}
{"line":2,"status_code":400,"message":"Invalid json message received"}
{"line":3,"status_code":400,"message":"Invalid json message received"}
{"line":4,"status_code":413,"message":"Line is too long."}
{"line":5,"status_code":413,"message":"Line is too long."}
{"line":6,"status_code":413,"message":"Line is too long."}
{"line":7,"status_code":413,"message":"Line is too long."}
{"line":8,"status_code":413,"message":"Line is too long."}
{"line":9,"status_code":413,"message":"Line is too long."}
{"line":10,"status_code":400,"message":"Invalid json message received"}
JSON;

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];


        $this->assertSame($expectedContent, $response['content']);
        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
    }

    public function testErrorWhenIdentifierIsMissing()
    {
        $data =
<<<JSON
    {"identifier": "my_identifier"}
    {"code": null}
    {"code": ""}
    {"code": " "}
    {}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"status_code":422,"message":"Code is missing."}
{"line":2,"status_code":422,"message":"Code is missing."}
{"line":3,"status_code":422,"message":"Code is missing."}
{"line":4,"status_code":422,"message":"Code is missing."}
{"line":5,"status_code":422,"message":"Code is missing."}
JSON;

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
    }

    public function testUpdateAttributeGroupWhenUpdaterFailed()
    {
        $data =
<<<JSON
    {"code": "foo", "type":"bar"}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"code":"foo","status_code":422,"message":"Property \"type\" does not exist. Check the standard format documentation.","_links":{"documentation":{"href":"http:\/\/api.akeneo.com\/api-reference.html#patch_attribute_groups__code_"}}}
JSON;

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
    }

    public function testUpdateAttributeWhenValidationFailed()
    {
        $data =
<<<JSON
    {"code": "foo,"}
JSON;

        $expectedContent =
<<<JSON
{"line":1,"code":"foo,","status_code":422,"message":"Validation failed.","errors":[{"property":"code","message":"Attribute group code may contain only letters, numbers and underscores"}]}
JSON;

        $response = $this->executeStreamRequest('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);
        $httpResponse = $response['http_response'];

        $this->assertSame(Response::HTTP_OK, $httpResponse->getStatusCode());
        $this->assertSame($expectedContent, $response['content']);
    }

    public function testPartialUpdateListWithBadContentType()
    {
        $data =
<<<JSON
    {"code": "my_code"}
JSON;

        $expectedContent =
<<<JSON
    {
        "code": 415,
        "message": "\"application\/json\" in \"Content-Type\" header is not valid. Only \"application\/vnd.akeneo.collection+json\" is allowed."
    }
JSON;

        $client = $this->createAuthenticatedClient();
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->request('PATCH', 'api/rest/v1/attribute-groups', [], [], [], $data);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expectedContent, $response->getContent());
    }

    /**
     * @return integer
     */
    protected function getBufferSize()
    {
        return $this->getParameter('api_input_buffer_size');
    }

    /**
     * @return integer
     */
    protected function getMaxNumberResources()
    {
        return $this->getParameter('api_input_max_resources_number');
    }

    /**
     * @param $code
     *
     * @return array
     */
    protected function getNormalizedAttributeGroup($code)
    {
        $attributeGroup = $this->get('pim_catalog.repository.attribute_group')->findOneByIdentifier($code);

        return $this->get('pim_catalog.normalizer.standard.attribute_group')->normalize($attributeGroup);
    }

    /**
     * @return Configuration
     */
    protected function getConfiguration()
    {
        return new Configuration([Configuration::getTechnicalCatalogPath()]);
    }
}
