<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Backend\ViewHelpers\Uri;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Use this ViewHelper to provide edit links (only the uri) to records. The ViewHelper will
 * pass the uid and table to FormEngine.
 *
 * The uid must be given as a positive integer.
 * For new records, use the newRecordViewHelper
 *
 * = Examples =
 *
 * <code title="URI to the record-edit action passed to FormEngine">
 * <be:uri.editRecord uid="42" table="a_table" returnUrl="foo/bar" />
 * </code>
 * <output>
 * /typo3/index.php?route=/record/edit&edit[a_table][42]=edit&returnUrl=foo/bar
 * </output>
 */
class EditRecordViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'uid of record to be edited, 0 for creation', true);
        $this->registerArgument('table', 'string', 'target database table', true);
        $this->registerArgument('returnUrl', 'string', '', false, '');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        if ($arguments['uid'] < 1) {
            throw new \InvalidArgumentException('Uid must be a positive integer, ' . $arguments['uid'] . ' given.', 1526128259);
        }
        if (empty($arguments['returnUrl'])) {
            $arguments['returnUrl'] = GeneralUtility::getIndpEnv('REQUEST_URI');
        }

        $params = [
            'edit' => [$arguments['table'] => [$arguments['uid'] => 'edit']],
            'returnUrl' => $arguments['returnUrl']
        ];
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute('record_edit', $params);
    }
}
