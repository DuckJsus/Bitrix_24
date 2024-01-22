<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data\Access;

use Accessmodule\Data\DB;

class Access
{
    // Типы доступов к полям
    const ACCESS_TYPE_INHERIT = 1;
    const ACCESS_TYPE_ALL = 2;
    const ACCESS_TYPE_THEIR_SUBSECTION = 3;
    const ACCESS_TYPE_THEIR_SECTION = 4;
    const ACCESS_TYPE_THEIR = 5;
    const ACCESS_TYPE_CLOSE = 6;

    protected $entityTypeId;
    protected $userId;
    protected $assignedId;

    /**
     * @param int $entityTypeId
     * @param int $userId
     * @param int $assignedId
     */
    public function __construct(int $entityTypeId, int $userId, int $assignedId)
    {
        $this->entityTypeId = $entityTypeId;
        $this->userId = $userId;
        $this->assignedId = $assignedId;
    }

    /**
     * Возвращает массив полей с типом доступа к каждому полю
     *
     * @return array
     */
    public function getFieldsAccess()
    {
        $arRoles = $this->getUserRoles();
        $arFields = DB::getByEntityTypeId('accessmodule_field', $this->entityTypeId);
        $relationshipWithAssigned = $this->getRelationshipBetweenDepartments();

        foreach ($arFields as $arField) {
            $arField['HIDDEN'] = false;

            foreach ($arRoles as $arRole) {
                switch ($arField[$arRole['NAME']]) {
                    case $this::ACCESS_TYPE_INHERIT:
                        break;
                    case $this::ACCESS_TYPE_ALL:
                        $arField['HIDDEN'] = false;
                        break;
                    case $this::ACCESS_TYPE_THEIR_SUBSECTION:
                        if ($this->userId == $this->assignedId
                            || $relationshipWithAssigned == $this::ACCESS_TYPE_THEIR_SUBSECTION) {

                            $arField['HIDDEN'] = false;
                            break;
                        }
                    case $this::ACCESS_TYPE_THEIR_SECTION:
                        if ($this->userId == $this->assignedId
                            || $relationshipWithAssigned == $this::ACCESS_TYPE_THEIR_SECTION) {

                            $arField['HIDDEN'] = false;
                            break;
                        }
                    case $this::ACCESS_TYPE_THEIR:
                        if ($this->userId == $this->assignedId) {

                            $arField['HIDDEN'] = false;
                            break;
                        }
                    case $this::ACCESS_TYPE_CLOSE:
                        $arField['HIDDEN'] = true;
                        break;
                }
            }
            $arReturnFields[$arField['NAME']] = $arField;
        }

        return $arReturnFields;
    }

    /**
     * Возвращает роли пользователя (если в бд есть сязанные с пользователем роли)
     *
     * @return mixed
     */
     private function getUserRoles() {
        // Группы пользователя
        $res = \CUser::GetUserGroupList($this->userId);
        while ($arGroup = $res->Fetch()){
            $arGroups[] = $arGroup['GROUP_ID'];
        }

        // Роли и связанные с ними пользователи/группы пользователей
        $arRoles = Role::get();
        $arRoleUsers = RoleUser::get(['USER_ID' => $this->userId]);
        $arRoleGroups = RoleGroup::getByGroups($arGroups);


         // Собираем массив ролей пользователя
        foreach ($arRoles as $arRole) {
            foreach ($arRoleUsers as $arRoleUser) {
                if ($arRole['ID'] === $arRoleUser['ROLE_ID']) {
                    $arSelectedRoles[$arRole['ID']] = $arRole;
                }
            }
            foreach ($arRoleGroups as $arRoleGroup) {
                if ($arRole['ID'] === $arRoleGroup['ROLE_ID']) {
                    $arSelectedRoles[$arRole['ID']] = $arRole;
                }
            }
        }
        ksort($arSelectedRoles);

        return $arSelectedRoles;
    }

    /**
     * Возвращает тип доступа, подходящий под отношения отделов Ответственного и Пользователя
     *
     * @return int
     */
    private function getRelationshipBetweenDepartments()
    {
        $structure = \CIntranetUtils::GetStructure();
        $tree = $structure['TREE'];

        $oUserinfo = \CUser::GetByID($this->userId);
        $userSection = $oUserinfo->Fetch()['UF_DEPARTMENT'][0];

        $oUserinfo = \CUser::GetByID($this->assignedId);
        $assignedSection = $oUserinfo->Fetch()['UF_DEPARTMENT'][0];

        $userSubsections = $this::getSubsections($tree, $userSection);

        if (in_array($assignedSection, $userSubsections)) {
            return $this::ACCESS_TYPE_THEIR_SUBSECTION;
        } elseif ($assignedSection == $userSection) {
            return $this::ACCESS_TYPE_THEIR_SECTION;
        } else {
            return $this::ACCESS_TYPE_THEIR;
        }
    }

    /**
     * Возвращает массив подотделов сотрудника
     *
     * @param $tree
     * @param $parentSection
     * @return array
     */
    private static function getSubsections($tree, $parentSection)
    {
        if (isset($tree[$parentSection])) {
            foreach ($tree[$parentSection] as $section) {
                $subsections[] = $section;
                $subsections = array_merge($subsections, self::getSubsections($tree, $section));
            }
        } else {
            return [];
        }

        return $subsections;
    }
}